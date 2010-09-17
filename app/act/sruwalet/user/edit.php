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
			$active = $bean->active;
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$login = $bean->login;
			$dormitoryId = $bean->dormitoryId;

			$bean->fillFromPost(self::PREFIX, array('password', 'referralStart', 'referralEnd'));
			$bean->modifiedById = $this->_srv->get('session')->authWaletAdmin;
			$bean->modifiedAt = NOW;

			if ($post['dormitory'] != $dormitoryId && $active) {
				throw UFra::factory('UFex_Dao_DataNotValid', 'Move active user', 0, E_WARNING, array('dormitory' => 'movedActive'));
			}
			if (isset($post['referralStart']) && $post['referralStart'] != '' && isset($post['referralEnd']) && $post['referralEnd'] != '') {
				throw UFra::factory('UFex_Dao_DataNotValid', 'Both referral dates', 0, E_WARNING, array('referralStart' => 'both'));
			}
			if (isset($post['referralEnd']) && $post['referralEnd'] == '') {
				if (!$bean->active) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Inactive without referral end', 0, E_WARNING, array('referralEnd' => 'inactive'));
				}
				$bean->referralEnd = 0;
			} else if (isset($post['referralEnd'])) {
				$bean->referralEnd = $post['referralEnd'];
			}
			if (isset($post['referralStart']) && $post['referralStart'] == '') {
				if ($bean->active) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Active without referral start', 0, E_WARNING, array('referralStart' => 'active'));
				}
				$bean->referralStart = 0;
			} else if (isset($post['referralStart'])) {
				$bean->referralStart = $post['referralStart'];
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

			if (!$active && $bean->active) {
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
