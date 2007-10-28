<?

/**
 * edycja danych uzytkownika
 */
class UFact_Sru_User_Edit
extends UFact {

	const PREFIX = 'userEdit';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->getFromSession();
			$bean->fillFromPost(self::PREFIX, array('login','password','name','surname'));
			$bean->save();

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		}
	}
}
