<?

/**
 * dodanie wlasnego komputera
 */
class UFact_Sru_Computer_Add
extends UFact {

	const PREFIX = 'computerAdd';

	public function go() {
		try {
			$user = UFra::factory('UFbean_Sru_User');
			$user->getFromSession();

			$computers = UFra::factory('UFbean_Sru_ComputerList');
			try {
				$computers->listByUserId($user->id);
				// znaleziono komputery, wiec uzytkownik nie moze dodac sobie kolejnego
				$this->markErrors(self::PREFIX, array('comp'=>'second'));
				return;
			} catch (UFex_Dao_NotFound $e) {
			} 

			try {
				$ip = UFra::factory('UFbean_Sru_Ipv4');
				$ip->getFreeByDormitoryIdAndVlan($user->dormitoryId, null);
			} catch (UFex_Dao_NotFound $e) {
				$this->markErrors(self::PREFIX, array('ip'=>'noFree'));
				return;
			}

			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->typeId = (array_key_exists($user->typeId, UFbean_Sru_Computer::$defaultUserToComputerType) ? UFbean_Sru_Computer::$defaultUserToComputerType[$user->typeId] : UFbean_Sru_Computer::TYPE_STUDENT);
			$post = $this->_srv->get('req')->post->{self::PREFIX};

			$foundOld = false;

			try {
				$bean->getInactiveByMacUserId($post['mac'], $user->id);
				$bean->active = true;
				$bean->lastActivated = NOW;
				$foundOld = true;
			} catch (UFex $e) {
				try {
					$bean->getInactiveByHostUserId($post['host'], $user->id);
					$bean->active = true;
					$bean->lastActivated = NOW;
					$foundOld = true;
				} catch (UFex $e) {
				}
			}
			// walidator locationId musi miec dane o akademiku
			$post['dormitory'] = $user->dormitoryId;
			$this->_srv->get('req')->post->{self::PREFIX} = $post;

			$bean->fillFromPost(self::PREFIX, null, array('mac', 'host'));
			if ($foundOld) {
				if ($bean->locationId != $user->locationId) {
					$this->_srv->get('req')->post->computerEdit = $this->_srv->get('req')->post->{self::PREFIX};	// walidator oczekuje computerEdit przy zmianie pokoju
					$bean->locationAlias = $user->locationAlias;
					$bean->locationId = $user->locationId;
					$this->_srv->get('req')->post->del('computerEdit');
				}
			} else {
				$bean->locationAlias = $user->locationAlias;
			}
			$bean->modifiedById = null;
			$bean->modifiedAt = NOW;
			$bean->userId = $user->id;
			$bean->ip = $ip->ip;
			$conf = UFra::shared('UFconf_Sru');
			$bean->availableTo = $conf->computerAvailableTo;
			$bean->save();

			if ($conf->sendEmail) {
				// wyslanie maila do usera
				$box = UFra::factory('UFbox_Sru');
				$sender = UFra::factory('UFlib_Sender');
				$title = $box->hostChangedMailTitle($bean, $user);
				$body = $box->hostChangedMailBody($bean, self::PREFIX, $user);
				$sender->send($user, $title, $body);
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
