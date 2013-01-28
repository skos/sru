<?php
/**
 * edycja administratora Waleta
 */
class UFact_SruWalet_Admin_Edit
extends UFact {

	const PREFIX = 'adminEdit';

	public function go() {
		try {
			$this->begin();
			$bean = UFra::factory('UFbean_SruWalet_Admin');
			$bean->getByPK((int)$this->_srv->get('req')->get->adminId);
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$acl = $this->_srv->get('acl');
			$login = $bean->login;
			$bean->fillFromPost(self::PREFIX, array('password', 'login', 'typeId', 'active'));
			$bean->modifiedById = $this->_srv->get('session')->authWaletAdmin;
				
			if(isset($post['password']) && $post['password'] != '' ) {
				$bean->password = $post['password'];
				$bean->lastPswChange = NOW;
				$bean->badLogins = 0;
			}

			if(isset($post['typeId']) && $acl->sruWalet('admin', 'advancedEdit')) {
				$bean->typeId = $post['typeId'];
			}
			if(isset($post['active']) && $acl->sruWalet('admin', 'advancedEdit')) {
				$bean->active = $post['active'];
			}

			$bean->save();

			while (!is_null(key($post['dorm'])) && $acl->sruWalet('admin', 'advancedEdit')) {
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

			if (!$bean->active) {
				try {
					$hosts = UFra::factory('UFbean_Sru_ComputerList');
					$hosts->updateCarerByCarerId($bean->id, null, $this->_srv->get('session')->authWaletAdmin);
				} catch (UFex $e) {
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
