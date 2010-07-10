<?

/**
 * edycja przez administratora danych uzytkownika
 */
class UFact_SruAdmin_User_Edit
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

			$bean->fillFromPost(self::PREFIX, array('password'));
			$bean->modifiedById = $this->_srv->get('session')->authAdmin;
			$bean->modifiedAt = NOW;
			if (isset($post['password']) && $post['password'] != '' ) {
				$map = UFra::factory('UFmap_Sru_User_Set');
				$valid = $map->valid('password');

				if (strlen($post['password'])<$valid['textMin']) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Password too short', 0, E_WARNING, array('password' => 'textMin'));
				} elseif (!isset($post['password2']) || $post['password'] != $post['password2']) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Data "password" and "password2" do not match', 0, E_WARNING, array('password' => 'mismatch'));
				}
				$bean->password = $bean->generatePassword($bean->login, $post['password']);
			}
			if (isset($post['facultyId']) && $post['facultyId'] == '0' && isset($post['studyYearId']) && $post['studyYearId'] != '0') {
				throw UFra::factory('UFex_Dao_DataNotValid', 'Data "studyYearId" differ from "N/A"', 0, E_WARNING, array('studyYearId' => 'noFaculty'));
			}
				$bean->facultyId = $post['facultyId'];
				$bean->studyYearId = $post['studyYearId'];

			$conf = UFra::shared('UFconf_Sru');
			if ($conf->checkWalet) {
				// sprawdzenie w bazie osiedla, jezeli admin nie wymusil zignorowania problemu
				if (!array_key_exists('ignoreWalet', $post) || 0 == $post['ignoreWalet']) {
					$walet = UFra::factory('UFbean_Sru_User');
					try {
						$walet->getFromWalet($bean->name, $bean->surname, $bean->locationAlias, $bean->dormitory);
					} catch (UFex_Dao_NotFound $e) {
						throw UFra::factory('UFex_Dao_DataNotValid', 'User not in Walet database', 0, E_WARNING,  array('walet' => 'notFound'));
					}
				}
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
			if ($login != $bean->login) {
				$this->_srv->get('msg')->set(self::PREFIX.'/loginChanged');
			}
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
