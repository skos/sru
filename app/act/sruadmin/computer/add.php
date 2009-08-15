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

			$bean = UFra::factory('UFbean_Sru_Computer');
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			if($post['ip'] == '') {
				try {
					$ip = UFra::factory('UFbean_Sru_Ipv4');
					$ip->getFreeByDormitoryId($user->dormitoryId);
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
			} catch (UFex $e) {
				try {
					$bean->getInactiveByHostUserId($post['host'], $user->id);
					$bean->active = true;
				} catch (UFex $e) {
				}
			}

			$bean->fillFromPost(self::PREFIX, null, array('mac', 'host', 'typeId'));
			$bean->locationId = $user->locationId;
			$bean->modifiedById = $this->_srv->get('session')->authAdmin;
			$bean->modifiedAt = NOW;
			$bean->userId = $user->id;
			$bean->ip = $ip->ip;
			$conf = UFra::shared('UFconf_Sru');
			// availableMaxTo musi byc ustawione przed availableTo
			$bean->availableMaxTo = $conf->computerAvailableMaxTo;
			$bean->availableTo = $conf->computerAvailableTo;
			$bean->save();

			// wyslanie maila do usera
			$box = UFra::factory('UFbox_SruAdmin');
			$title = $box->hostChangedMailTitle($bean);
			$body = $box->hostChangedMailBody($bean, self::PREFIX);
			$headers = $box->hostChangedMailHeaders($bean);
			mail($user->email, $title, $body, $headers);

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
