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
			$bean->modifiedById = $this->_srv->get('session')->authWaletAdmin;
			$bean->gg = '';
			$bean->studyYearId = 0;

			// tworzymy login
			$user = UFra::factory('UFbean_Sru_User');
			$login = strtolower(substr($bean->name, 0, 2).substr($bean->surname, 0, 3));
			$used = true;
			try {
				$user->getByLogin($login);
			} catch (UFex_Dao_NotFound $e) {
				$used = false;
			}
			if ($used) {
				for($i = 1; $i < 100; $i++) {
					try {
						$user->getByLogin($login.$i);
					} catch (UFex_Dao_NotFound $e) {
						$used = false;
						break;
					}
				}
				$bean->login = $login.$i;
			} else {
				$bean->login = $login;
			}

			$post = $this->_srv->get('req')->post->{self::PREFIX};

			// wygenerowanie hasla
			$password = md5($bean->login.NOW);
			$password = base_convert($password, 16, 35);
			$password = substr($password, 0, 8);
			$bean->password = $bean->generatePassword($bean->login, $password);
				
			$id = $bean->save();
			$req = $this->_srv->get('req');
			$req->get->userId = $id;

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
