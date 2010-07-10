<?

/**
 * dodanie uzytkownika
 */
class UFact_Sru_User_Add
extends UFact {

	const PREFIX = 'userAdd';

	public function go() {
		try {
			$this->begin();
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->fillFromPost(self::PREFIX);
			$bean->active = false;
			$post = $this->_srv->get('req')->post->{self::PREFIX};

			if (isset($post['facultyId']) && $post['facultyId'] == '0' && isset($post['studyYearId']) && $post['studyYearId'] != '0') {
				throw UFra::factory('UFex_Dao_DataNotValid', 'Data "studyYearId" differ from "N/A"', 0, E_WARNING, array('studyYearId' => 'noFaculty'));
			}
			// wygenerowanie hasla
			$password = md5($bean->login.NOW);
			$password = base_convert($password, 16, 35);
			$password = substr($password, 0, 8);
			$bean->password = $bean->generatePassword($bean->login, $password);

			// sprawdzenie w bazie osiedla
			$walet = UFra::factory('UFbean_Sru_User');
			$conf = UFra::shared('UFconf_Sru');
			if ($conf->checkWalet) {
				// sprawdzenie w bazie osiedla
				$walet = UFra::factory('UFbean_Sru_User');
				try {
					$walet->getFromWalet($bean->name, $bean->surname, $bean->locationAlias, $bean->dormitory);
				} catch (UFex_Dao_NotFound $e) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'User not in Walet database', 0, E_WARNING,  array('walet' => 'notFound'));
				}
			}

			$id = $bean->save();
			$bean->getByPK($id);	// uzupelnione dane dociagane z innych tabel
			$req = $this->_srv->get('req');

			// wyslanie maila
			$box = UFra::factory('UFbox_Sru');
			$sender = UFra::factory('UFlib_Sender');
			$title = $box->userAddMailTitle($bean);
			$body = $box->userAddMailBody($bean, $password);
			$sender->send($bean, $title, $body);

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
