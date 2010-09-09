<?

/**
 * dodanie uzytkownika przez administratora Waleta
 */
class UFact_SruWalet_User_Add
extends UFact {

	const PREFIX = 'userAdd';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->fillFromPost(self::PREFIX, array('password'));
			$bean->modifiedById = $this->_srv->get('session')->authAdmin;
			$bean->gg = '';
			$post = $this->_srv->get('req')->post->{self::PREFIX};
		
			if (isset($post['facultyId']) && $post['facultyId'] == '0' && isset($post['studyYearId']) && $post['studyYearId'] != '0') {
				throw UFra::factory('UFex_Dao_DataNotValid', 'Data "studyYearId" differ from "N/A"', 0, E_WARNING, array('studyYearId' => 'noFaculty'));
			}

			// wygenerowanie hasla
			$password = md5($bean->login.NOW);
			$password = base_convert($password, 16, 35);
			$password = substr($password, 0, 8);
			$bean->password = $bean->generatePassword($bean->login, $password);

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
				
			$id = $bean->save();
			$bean->getByPK($id);	// uzupelnione dane dociagane z innych tabel
			$req = $this->_srv->get('req');
			$req->get->userId = $id;

			// wyslanie maila
			$box = UFra::factory('UFbox_Sru');
			$sender = UFra::factory('UFlib_Sender');
			$title = $box->userAddMailTitle($bean);
			$body = $box->userAddMailBody($bean, $password);
			$sender->send($bean, $title, $body, self::PREFIX);

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
