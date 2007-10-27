<?

/**
 * wylogowanie uzytkownika
 */
class UFact_Sru_User_Logout
extends UFact {

	const PREFIX = 'userLogin';

	public function go() {
		try {
			$this->_srv->get('session')->del('auth');
			
			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex $e) {
		}
	}
}
