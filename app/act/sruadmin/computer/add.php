<?php

/**
 * dodanie cudzego komputera
 */
class UFact_SruAdmin_Computer_Add
extends UFact {

	const PREFIX = 'computerAdd';

	public function go() {
		try {
			$user = UFra::factory('UFbean_Sru_User');  
			$user->getByPK((int)$this->_srv->get('req')->get->userId);

			$acl = $this->_srv->get('acl');
			if(!$acl->sruAdmin('computer', 'addForUser', $user->id)) {
				UFra::error('Host cannot be registered for inactive user');
				return;
			}

			$bean = UFra::factory('UFbean_Sru_Computer');
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			if($post['ip'] == '') {
				try {
					$ip = UFra::factory('UFbean_Sru_Ipv4');
					$dormitory = null;
					$vlan = $bean->getVlanByComputerType($post['typeId']);
					$useDorm = false;
					if ($vlan == UFbean_SruAdmin_Vlan::DEFAULT_VLAN) {
						$dormitory = UFra::factory('UFbean_Sru_Dormitory');
						$dormitory->getByPK($user->dormitoryId);
						$vlan = $dormitory->vlan;
						$useDorm = true;
					}
					$dormId = $useDorm ? $dormitory->id : null;
					$ip->getFreeByDormitoryIdAndVlan($dormId, $vlan, $useDorm);
				} catch (UFex_Dao_NotFound $e) {
					$this->markErrors(self::PREFIX, array('ip'=>'noFreeAdmin'));
					return;
				}
			} else {
				try {
					$ip = UFra::factory('UFbean_Sru_Ipv4');
					$ip->getByIp($post['ip']);
				} catch (UFex_Dao_NotFound $e) {
					$this->markErrors(self::PREFIX, array('ip'=>'notFound'));
					return;
				} catch (UFex_Db_QueryFailed $e) {
					$this->markErrors(self::PREFIX, array('ip'=>''));
					return;
				}
			}

			try {
				$bean->getInactiveByMacUserId($post['mac'], $user->id);
				$bean->active = true;
				$bean->lastActivated = NOW;
			} catch (UFex $e) {
				try {
					$bean->getInactiveByHostUserId($post['host'], $user->id);
					$bean->active = true;
					$bean->lastActivated = NOW;
				} catch (UFex $e) {
				}
			}

			$bean->fillFromPost(self::PREFIX, null, array('mac', 'host', 'typeId', 'masterHostId', 'skosCarerId', 'waletCarerId'));
			if ($bean->masterHostId == 0) {
				$bean->masterHostId = null;
			}
			if (!is_null($post['skosCarerId']) && $post['skosCarerId'] != 0) {
				$bean->carerId = $post['skosCarerId'];
			} else if (!is_null($post['waletCarerId']) && $post['waletCarerId'] != 0) {
				$bean->carerId = $post['waletCarerId'];
			} else {
				$bean->carerId = null;
			}
			if ($bean->typeId == UFbean_Sru_Computer::TYPE_STUDENT_OTHER) {
				$bean->autoDeactivation = false;
			}
			$bean->locationId = $user->locationId;
			$bean->modifiedById = $this->_srv->get('session')->authAdmin;
			$bean->modifiedAt = NOW;
			$bean->userId = $user->id;
			$bean->ip = $ip->ip;
			$conf = UFra::shared('UFconf_Sru');
			$bean->availableTo = $conf->computerAvailableTo;
			$id = $bean->save();

			if ($conf->sendEmail) {
				$bean->getByPK($id);	// pobranie nowych danych, np. aliasu ds-u
				// wyslanie maila do usera
				$box = UFra::factory('UFbox_SruAdmin');
				$sender = UFra::factory('UFlib_Sender');

				if ($bean->typeId == UFbean_Sru_Computer::TYPE_SERVER || $bean->typeId == UFbean_Sru_Computer::TYPE_SERVER_VIRT) {
					$admin = UFra::factory('UFbean_SruAdmin_Admin');
					$admin->getByPK($this->_srv->get('session')->authAdmin);
					
					// admin, który został opiekunem, powinien dostać maila
					$title = $box->carerChangedToYouMailTitle($bean);
					$body = $box->carerChangedToYouMailBody($bean, $admin);
					$newCarer = UFra::factory('UFbean_SruAdmin_Admin');
					$newCarer->getByPK($bean->carerId);
					$sender->send($newCarer, $title, $body, self::PREFIX);
				}
				
				$title = $box->hostChangedMailTitle($bean, $user);
				$body = $box->hostChangedMailBody($bean, self::PREFIX, $user);
				$sender->send($user, $title, $body, self::PREFIX);
			}

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex_Db_QueryFailed $e) {
			if (0 == $e->getCode()) {
				$this->markErrors(self::PREFIX, array('mac'=>'regexp'));
			} else {
				throw $e;
			}
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
