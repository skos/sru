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
			$this->begin();

			$bean = UFra::factory('UFbean_Sru_User');
			$bean->getByPK((int)$this->_srv->get('req')->get->userId);

			$active = $bean->active;
			$referralStart = $bean->referralStart;

			$dormitoryId = $bean->dormitoryId;

			$bean->fillFromPost(self::PREFIX, array('password', 'referralStart', 'dormitory', 'nationalityName'));
			$bean->referralEnd = 0;
			$bean->modifiedById = $this->_srv->get('session')->authWaletAdmin;
			$bean->modifiedAt = NOW;

			if ($post['dormitory'] != $dormitoryId && $active) {
				throw UFra::factory('UFex_Dao_DataNotValid', 'Move active user', 0, E_WARNING, array('dormitory' => 'movedActive'));
			}
			
			if (isset($post['referralStart']) && $post['referralStart'] == '') {
				if ($bean->active) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Active without referral start', 0, E_WARNING, array('referralStart' => 'active'));
				}
				$bean->referralStart = 0;
			} else if (isset($post['referralStart'])) {
				$bean->referralStart = $post['referralStart'];
			}
			if ((!$active && $bean->active) || $referralStart != $bean->referralStart) {
				$bean->updateNeeded = true;
			}

			if($post['pesel'] == '') {
				$bean->pesel = null;
			}
			
			// zapis narodowości
			if($post['nationalityName'] != '') {
				try {
					$country = UFra::factory('UFbean_SruWalet_Country');
					$country->getByName(mb_convert_case($post['nationalityName'], MB_CASE_LOWER, "UTF-8"));
					$countryId = $country->id;
				} catch (UFex_Dao_NotFound $e) {
					$country->nationality = mb_convert_case($post['nationalityName'], MB_CASE_LOWER, "UTF-8");
					$countryId = $country->save();
				}
			} else {
				$countryId = null;
			}
			$bean->nationality = $countryId;

			$bean->save();

			try {
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
				}
				$typeId = (array_key_exists($bean->typeId, UFtpl_Sru_Computer::$userToComputerType) ? UFtpl_Sru_Computer::$userToComputerType[$bean->typeId] : UFbean_Sru_Computer::TYPE_STUDENT);
				$comps->updateLocationAndTypeByUserId($bean->id, $bean->locationId, $typeId, $waletAdmin);
			} catch (UFex_Dao_NotFound $e) {
				// uzytkownik nie ma komputerow
			}

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
