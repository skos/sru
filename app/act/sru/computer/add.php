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

			try {
				$ip = UFra::factory('UFbean_Sru_Ipv4');
				$ip->getFreeByDormitoryId($user->dormitoryId);
			} catch (UFex_Dao_NotFound $e) {
				$this->markErrors(self::PREFIX, array('ip'=>'noFree'));
				return;
			}

			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->fillFromPost(self::PREFIX, null, array('mac', 'host'));
			$bean->locationId = $user->locationId;
			$bean->modifiedById = null;
			$bean->modifiedAt = NOW;
			$bean->userId = $user->id;
			$bean->ip = $ip->ip;
			$conf = UFra::shared('UFconf_Sru');
			$bean->availableTo = $conf->computerAvailableTo;
			$bean->availableMaxTo = $conf->computerAvailableMaxTo;
			$bean->save();

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex_Db_QueryFailed $e) {
		print_r($e);
			$this->markErrors(self::PREFIX, array('mac'=>'regexp'));
		}
	}
}
