<?

/**
 * edycja przez administratora danych uzytkownika
 */
class UFact_SruAdmin_User_Edit
extends UFact {

	const PREFIX = 'userEdit';

	public function go() {
		try {
			$acl = $this->_srv->get('acl');

			$this->begin();
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->getByPK((int)$this->_srv->get('req')->get->userId);
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$login = $bean->login;
			$dormitoryId = $bean->dormitoryId;
			$active = $bean->active;

			$bean->fillFromPost(self::PREFIX, array('password', 'studyYearId'));
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
			if (isset($post['dormitory']) && $dormitoryId != $post['dormitory'] && !$acl->sruAdmin('user', 'fullEdit', $bean->id)) {
				UFra::error('This user`s location cannot be edited by admin');
				return;
			}
			if (isset($post['dormitory'])) {
				$bean->dormitory = $post['dormitory'];
			}
			if (!is_null($bean->studyYearId) && isset($post['studyYearId']) && $post['studyYearId'] == '') {
				throw UFra::factory('UFex_Dao_DataNotValid', 'Cannot set empty year of study', 0, E_WARNING, array('studyYearId' => ''));
			}
			if (isset($post['studyYearId']) && $post['studyYearId'] != '') {
				$bean->studyYearId = $post['studyYearId'];
			}
			if (($active && $bean->active == false) && !$acl->sruAdmin('user', 'fullEdit', $bean->id) && $acl->sruAdmin('user', 'add')) {
				throw UFra::factory('UFex_Dao_DataNotValid', 'Activate/deactivate not-SKOS user', 0, E_ERROR, array('active' => 'invalid'));
			}					

			$bean->modifiedById = $this->_srv->get('session')->authAdmin;
			$bean->modifiedAt = NOW;
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
