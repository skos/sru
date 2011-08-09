<?

/**
 * dodanie uzytkownika przez administratora SKOS
 */
class UFact_SruAdmin_User_Add
extends UFact {

	const PREFIX = 'userAdd';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->fillFromPost(self::PREFIX, array('password'));

			$bean->modifiedById = $this->_srv->get('session')->authAdmin;
			$bean->gg = '';

			$bean->updateNeeded = true;
			$bean->changePasswordNeeded = true;

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
