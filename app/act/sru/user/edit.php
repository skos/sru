<?

/**
 * edycja daych uzytkownika
 */
class UFact_Sru_User_Edit
extends UFact {

	const PREFIX = 'userEdit';

	protected function checkOldPassword(&$bean, &$post) {
		if ($bean->password != $bean->generatePassword($bean->login, $post['password3'])) {
			throw UFra::factory('UFex_Dao_DataNotValid', 'Data "password3" is not valid', 0, E_WARNING, array('password3' => 'invalid'));
		}
	}
	
	public function go() {
		try {
			$this->begin();
			$bean = UFra::factory('UFbean_Sru_User');
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$bean->getFromSession();

			// jesli nowe konto lub wygenerowane haslo, niech zaktualizuje hasło!
			if (is_null($bean->email) || $bean->email == '' || $bean->changePasswordNeeded) {
				if (!isset($post['password']) || $post['password'] == '' ) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Need to set new password', 0, E_WARNING, array('password' => 'needNewOne'));
				}
			}
			// jesli nie ma maila, to niech poda!
			if (!isset($post['email']) || $post['email'] == '') {
				throw UFra::factory('UFex_Dao_DataNotValid', 'Email not null', 0, E_WARNING, array('email' => 'notnull'));
			}
			// jesli nowe konto, to dostanie maila powitalnego!
			$newAccount = false;
			if (is_null($bean->email) || $bean->email == '') {
				$newAccount = true;
			}

			$bean->fillFromPost(self::PREFIX, array('email', 'login','password','facultyId','studyYearId'));
			if (isset($post['email']) && $post['email'] != $bean->email) {
				$this->checkOldPassword($bean, $post);
				if (!isset($post['email2']) || $post['email'] != $post['email2']){
					throw UFra::factory('UFex_Dao_DataNotValid', 'Data "email" and "email2" do not match', 0, E_WARNING, array('email' => 'mismatch'));
				}
				$bean->email = $post['email'];
			}
			if (isset($post['password']) && $post['password'] != '' ) {
				$this->checkOldPassword($bean, $post);
			
				$map = UFra::factory('UFmap_Sru_User_Set');
				$valid = $map->valid('password');

				if (!isset($post['password2']) || $post['password'] != $post['password2']) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Data "password" and "password2" do not match', 0, E_WARNING, array('password' => 'mismatch'));
				}
				if (strlen($post['password2']) < $valid['textMin']) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Data "password" too short', 0, E_WARNING, array('password' => 'tooShort'));
				}
				$bean->password = $bean->generatePassword($bean->login, $post['password']);
			}
			if (isset($post['facultyId']) && $post['facultyId'] == '0' && isset($post['studyYearId']) && $post['studyYearId'] != '0') {
				throw UFra::factory('UFex_Dao_DataNotValid', 'Data "studyYearId" differ from "N/A"', 0, E_WARNING, array('studyYearId' => 'noFaculty'));
			}
			if(isset($post['studyYearId'])) {
				$bean->studyYearId = $post['studyYearId'];
			}
			$bean->updateNeeded = false;
			$bean->changePasswordNeeded = false;

			$bean->modifiedById = null;
			$bean->modifiedAt = NOW;

			$bean->save();

			$conf = UFra::shared('UFconf_Sru');
			if ($conf->sendEmail) {
				// wyslanie maila do usera
				$box = UFra::factory('UFbox_Sru');
				$sender = UFra::factory('UFlib_Sender');
				$bean->getByPK($bean->id);	// pobranie nowych danych, np. aliasu ds-u
				if($newAccount) {
					$title = $box->userAddMailTitle($bean);
					$body = $box->userAddMailBody($bean);
					$sender->send($bean, $title, $body);
				}
				$title = $box->dataChangedMailTitle($bean);
				$body = $box->dataChangedMailBody($bean);
				$sender->send($bean, $title, $body);
			}
	
			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			$this->commit();
		} catch (UFex_Dao_DataNotValid $e) {
			$this->rollback();
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			$this->rollback();
			UFra::error($e);
		}
	}
}
