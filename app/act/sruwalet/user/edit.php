<?

/**
 * edycja przez administratora Waleta danych uzytkownika
 */
class UFact_SruWalet_User_Edit
extends UFact {

	const PREFIX = 'userEdit';

	public function go() {
		try {
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			
			$bean = UFra::factory('UFbean_Sru_User');
			$userId = (int)$this->_srv->get('req')->get->userId;
			$bean->getByPK($userId);
			
			$active = $bean->active;
			$referralStart = $bean->referralStart;
			$locationAlias = $bean->locationAlias;
			
			$dormitoryId = $bean->dormitoryId;
			
			$this->begin();

			$bean->fillFromPost(self::PREFIX, array('password', 'dormitory', 'nationalityName', 'lastLocationChangeActive'));
			$bean->toDeactivate = false;
			$bean->modifiedById = $this->_srv->get('session')->authWaletAdmin;
			$bean->modifiedAt = NOW;

			if ($post['dormitory'] != $dormitoryId && $active) {
				throw UFra::factory('UFex_Dao_DataNotValid', 'Move active user', 0, E_WARNING, array('dormitory' => 'movedActive'));
			}
			
			if ((!$active && $bean->active) || $referralStart != $bean->referralStart) {
				$bean->updateNeeded = true;
				$bean->lastLocationChange = $bean->referralStart;
			}
			if ($active && $bean->active && $locationAlias != $bean->locationAlias) {
				if (array_key_exists('lastLocationChangeActive', $post) && !UFbean_Sru_User::isDate($post['lastLocationChangeActive'])) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Move active user', 0, E_WARNING, array('lastLocationChangeActive' => 'invalid'));
				}
				$bean->lastLocationChange = $post['lastLocationChangeActive'];
			}

			if($post['pesel'] == '') {
				$bean->pesel = null;
			}
			
			// zapis narodowości
			if($post['nationalityName'] != '') {
				$country = UFra::factory('UFbean_SruWalet_Country');
				$nationality = mb_convert_case(trim($post['nationalityName']), MB_CASE_LOWER, "UTF-8");
				try {
					$country->getByName($nationality);
					$countryId = $country->id;
				} catch (UFex_Dao_NotFound $e) {
					$country->nationality = $nationality;
					$countryId = $country->save();
				}
			} else {
				$countryId = null;
			}
			$bean->nationality = $countryId;
			
			if (isset($post['printConfirmation']) && $post['printConfirmation']) {
				// wygenerowanie hasla
				$password = md5($bean->login.NOW);
				$password = base_convert($password, 16, 35);
				$password = substr($password, 0, 8);
				$bean->password = $bean->generatePassword($bean->login, $password);
				$req = $this->_srv->get('req');
				$req->get->password = $password;
				$req->get->printConfirmation = true;
			}

			$bean->save();

			$conf = UFra::shared('UFconf_Sru');
			if ($conf->sendEmail && $bean->notifyByEmail() && !is_null($bean->email) && $bean->email != '') {
				$sender = UFra::factory('UFlib_Sender');
				$history = UFra::factory('UFbean_SruAdmin_UserHistoryList');
				$history->listByUserId($bean->id, 1);
				$bean->getByPK($bean->id);	// pobranie nowych danych, np. aliasu ds-u
				// wyslanie maila do usera
				if (!$active && $bean->active) { // jesli aktywowane konto, to wyslijmy mu maila powitalnego
					$box = UFra::factory('UFbox_Sru');
					$title = $box->userAddMailTitle($bean);
					$body = $box->userAddMailBody($bean);
					$sender->send($bean, $title, $body);
				}
				$box = UFra::factory('UFbox_SruAdmin');
				$title = $box->dataChangedMailTitle($bean);
				$body = $box->dataChangedMailBody($bean, $history);
				$sender->send($bean, $title, $body, self::PREFIX);
			}

			if ((!$active && $bean->active) || ((is_null($referralStart) || $referralStart == '') && !is_null($bean->referralStart) && $bean->referralStart != '')) {
				$req = $this->_srv->get('req');
				$req->get->activated = true;
			}

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			$this->commit();
			
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->getByPK($userId);
			
			/* outside transaction - computers operations, they aren't critical - no problems if fail */
			try {
				$this->begin();
				$comps = UFra::factory('UFbean_Sru_ComputerList');
				$waletAdmin = $this->_srv->get('session')->authWaletAdmin;
				if(!$active && $bean->active){
					try{
						if($post['dormitory'] != $bean->dormitoryId){
							$comps->restoreWithUser($bean->id, true, $waletAdmin);
						}else{
							$comps->restoreWithUser($bean->id, false, $waletAdmin);
						}
					}catch(Exception $e){
						//po prostu ma nic nie wyświetlić, gdy coś się nie uda, można dorobić obsługę Exceptiona w tym miejscu
					}
				} else {
					$comps->listByUserId($bean->id);
					foreach ($comps as $comp) {
						$computer = UFra::factory('UFbean_Sru_Computer');
						$computer->getByPK($comp['id']);
						$computer->typeId = UFbean_Sru_Computer::getHostType($bean, $computer);
						$computer->locationId = $bean->locationId;
						$computer->modifiedById = $waletAdmin;
						$computer->modifiedAt = NOW;
						$computer->save();
					}					
				}
				$this->commit();
			} catch (UFex_Dao_NotFound $e) {
				// uzytkownik nie ma komputerow
			} catch (UFex_Dao_DataNotValid $e) {
				$this->rollback();
			} catch (UFex_Db_QueryFailed $e) {
				$this->rollback();
			}
			
			if($dormitoryId != $bean->dormitoryId || $locationAlias != $bean->locationAlias){
				UFlib_Helper::removePenaltyFromPort($userId);
			}
			
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
