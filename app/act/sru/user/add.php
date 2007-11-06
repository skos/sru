<?

/**
 * dodanie uzytkownika i od razu go loguje do systemu
 */
class UFact_Sru_User_Add
extends UFact {

	const PREFIX = 'userAdd';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->fillFromPost(self::PREFIX);
			$id = $bean->save();
			$this->_srv->get('session')->auth = $id;

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		}
	}
}
