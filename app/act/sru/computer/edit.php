<?

/**
 * edycja danych wlasnego komputera
 */
class UFact_Sru_Computer_Edit
extends UFact {

	const PREFIX = 'computerEdit';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->getByUserIdPK((int)$this->_srv->get('session')->auth, (int)$this->_srv->get('req')->get->computerId);
			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($bean->userId);
			$bean->fillFromPost(self::PREFIX, null, array('mac', 'host'));
			if (!$bean->active) {
				// przywrocenie aktywnosci komputera
				$computers = UFra::factory('UFbean_Sru_ComputerList');
				try {
					$computers->listByUserId($user->id);
					// znaleziono komputery, wiec uzytkownik nie moze dodac sobie kolejnego
					$this->markErrors(self::PREFIX, array('comp'=>'second'));
					return;
				} catch (UFex_Dao_NotFound $e) {
				}
				$bean->active = true;
				$conf = UFra::shared('UFconf_Sru');
				$bean->availableTo = $conf->computerAvailableTo;
				$bean->lastActivated = NOW;
				// aktualizacja lokalizacji komputera
				$bean->locationId = $user->locationId;
				// aktualizacja typu kompa wg typu usera
				$bean->typeId = UFbean_Sru_Computer::getHostType($user, $bean);
				// przypisanie nowego IP
				try {
					$ip = UFra::factory('UFbean_Sru_Ipv4');
					$ip->getFreeByDormitoryIdAndVlan($user->dormitoryId, null);
					$bean->ip = $ip->ip;
				} catch (UFex_Dao_NotFound $e) {
					$this->rollback();
					$this->markErrors(self::PREFIX, array('ip'=>'noFree'));
					return;
				}
			}
			$bean->modifiedById = null;
			$bean->modifiedAt = NOW;
			$bean->save();

			$conf = UFra::shared('UFconf_Sru');
			if ($conf->sendEmail) {
				// wyslanie maila do usera
				$box = UFra::factory('UFbox_Sru');
				$sender = UFra::factory('UFlib_Sender');
				$bean->getByPK($bean->id);	// pobranie nowych danych, np. aliasu ds-u
				$title = $box->hostChangedMailTitle($bean, $user);
				$body = $box->hostChangedMailBody($bean, self::PREFIX, $user);
				$sender->send($user, $title, $body);
			}

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
