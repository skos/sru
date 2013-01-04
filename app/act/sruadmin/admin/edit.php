<?php
/**
 * edycja administratora
 */
class UFact_SruAdmin_Admin_Edit
extends UFact {

	const PREFIX = 'adminEdit';

	public function go() {
		try {
			$this->begin();
			$bean = UFra::factory('UFbean_SruAdmin_Admin');
			$bean->getByPK((int)$this->_srv->get('req')->get->adminId);
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$acl = $this->_srv->get('acl');
			$login = $bean->login;
			$bean->fillFromPost(self::PREFIX, array('password', 'login', 'typeId', 'active'));

			if (isset($post['displayUsers'])) {
				if($post['displayUsers'] == 1) {
					setcookie('SRUDisplayUsers', '1', time() + 60*60*24*3650, '/');
				}else{
					setcookie('SRUDisplayUsers', '0', time() + 60*60*24*3650, '/');
				}
			}
				
			if(isset($post['password']) && $post['password'] != '' ) {
				$bean->password = $post['password'];
				$bean->lastPswChange = NOW;
				
				//TODO #673
				$bean->passwordBlow = UFbean_SruAdmin_Admin::generateBlowfishPassword($post['password']);
			}
			
			if(isset($post['typeId']) && $acl->sruAdmin('admin', 'advancedEdit')) {
				$bean->typeId = $post['typeId'];	
			}	
			if(isset($post['active']) && $acl->sruAdmin('admin', 'advancedEdit')) {
				$bean->active = $post['active'];	
			}
			$bean->modifiedById = $this->_srv->get('session')->authAdmin;
			$bean->modifiedAt = NOW;
			$bean->save();

			if (array_key_exists('dorm', $post)) {
				while (!is_null(key($post['dorm'])) && $acl->sruAdmin('admin', 'changeAdminDorms', $bean->id)) {
					if (current($post['dorm'])) {

						try {
							$admDorm = UFra::factory('UFbean_SruWalet_AdminDormitory');
							$admDorm->getByAdminAndDorm($bean->id, key($post['dorm']));
						} catch (UFex $e) {
							$admDorm->admin = $bean->id;
							$admDorm->dormitory = key($post['dorm']);
							$admDorm->save();
						}
					} else {
						try {
							$admDorm = UFra::factory('UFbean_SruWalet_AdminDormitory');
							$admDorm->getByAdminAndDorm($bean->id, key($post['dorm']));
							$admDorm->del();
						} catch (UFex $e) {
						}
					}
					next($post['dorm']);
				}
			}

			$this->commit();

			$this->begin();
			// zapis godzin dyzurow
			for ($i = 1; $i <= 7; $i++) {
				$dh = $post['dutyHours'][$i];
				if (!is_null($dh) && $dh != '') {
					$hours = str_replace(' ', '', trim($dh));
					$hours = str_replace(':', '', $hours);
					$hours = explode('-', $hours);
					$startHour = '';
					$endHour = '';
					if (isset($hours[0]) && isset($hours[1])) {
						$startHour = $hours[0];
						$endHour = $hours[1];
					}
					if ((int)$startHour >= (int)$endHour) {
						throw UFra::factory('UFex_Dao_DataNotValid', 'End hour <= start hour', 0, E_WARNING,  array('endHour' => 'wrong'));
					}

					try {
						$currentDh = UFra::factory('UFbean_SruAdmin_DutyHours');
						$currentDh->getByAdminIdAndDay($bean->id, $i);
						$currentDh->startHour = $startHour;
						$currentDh->endHour = $endHour;
						$currentDh->comment = $post['dhComment'][$i];
						$currentDh->active = $post['dhActive'][$i];
						$currentDh->save();
					} catch (UFex $e) {
						$currentDh = UFra::factory('UFbean_SruAdmin_DutyHours');
						$currentDh->startHour = $startHour;
						$currentDh->endHour = $endHour;
						$currentDh->day = $i;
						$currentDh->adminId = $bean->id;
						$currentDh->comment = $post['dhComment'][$i];
						$currentDh->active = $post['dhActive'][$i];
						$currentDh->save();
					}
				} else {
					try {
						$currentDh = UFra::factory('UFbean_SruAdmin_DutyHours');
						$currentDh->getByAdminIdAndDay($bean->id, $i);
						$currentDh->del();
					} catch (UFex $e) {
					}
				}
			}

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			$this->commit();
		} catch (UFex_Dao_DataNotValid $e) {
			$this->rollback();
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			$this->rollback();
			UFra::error($e);
		}
	}
}
