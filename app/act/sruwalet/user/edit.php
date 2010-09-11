<?

/**
 * edycja przez administratora Waleta danych uzytkownika
 */
class UFact_SruWalet_User_Edit
extends UFact {

	const PREFIX = 'userEdit';

	public function go() {
		try {
			$this->begin();
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->getByPK((int)$this->_srv->get('req')->get->userId);
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$login = $bean->login;
			$dormitoryId = $bean->dormitoryId;

			$bean->fillFromPost(self::PREFIX, array('password', 'referralEnd'));
			$bean->modifiedById = $this->_srv->get('session')->authWaletAdmin;
			$bean->modifiedAt = NOW;
			if (isset($post['referralEnd']) && $post['referralEnd'] == '') {
				$bean->referralEnd = 0;
			} else if (isset($post['referralEnd'])) {
				$bean->referralEnd = $post['referralEnd'];
			}

			if ($this->_srv->get('req')->post->{self::PREFIX}['changeComputersLocations']) {
				try {
					$comps = UFra::factory('UFbean_Sru_ComputerList');
					$comps->listByUserId($bean->id);
					foreach ($comps as $comp) {
						try {
							$computer = UFra::factory('UFbean_Sru_Computer');
							$computer->getByHost($comp['host']);
							$ipAddr = $computer->ip;
							if ($dormitoryId != $bean->dormitory) {
								$ip = UFra::factory('UFbean_Sru_Ipv4');
								$ip->getFreeByDormitoryId(($bean->dormitory));
								$ipAddr = $ip->ip;
							}
							$computer->updateLocationByHost($comp['host'], $bean->locationId, $ipAddr, $this->_srv->get('session')->authAdmin);
						} catch (UFex_Dao_NotFound $e) {
							throw UFra::factory('UFex_Dao_DataNotValid', 'No free IP', 0, E_WARNING, array('ip'=>'noFreeAdmin'));
						}
					}
				} catch (UFex_Dao_NotFound $e) {
					// uzytkownik nie ma komputerow
				}
			}

			$bean->save();

			$conf = UFra::shared('UFconf_Sru');
			if ($conf->sendEmail && $bean->notifyByEmail()) {
				$history = UFra::factory('UFbean_SruAdmin_UserHistoryList');
				$history->listByUserId($bean->id, 1);
				$bean->getByPK($bean->id);	// pobranie nowych danych, np. aliasu ds-u
				// wyslanie maila do usera
				$box = UFra::factory('UFbox_SruAdmin');
				$sender = UFra::factory('UFlib_Sender');
				$title = $box->dataChangedMailTitle($bean);
				$body = $box->dataChangedMailBody($bean, $history);
				$sender->send($bean, $title, $body, self::PREFIX);
			}

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			$this->commit();
		} catch (UFex_Dao_DataNotValid $e) {
			$this->rollback();
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex_Db_QueryFailed $e) {
			$this->rollback();
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
