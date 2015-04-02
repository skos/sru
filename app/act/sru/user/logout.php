<?

/**
 * wylogowanie uzytkownika
 */
class UFact_Sru_User_Logout
extends UFact {

	const PREFIX = 'userLogin';

	public function go() {
		try {
			$sess = $this->_srv->get('session');
			$sess->del('auth');
			$sess->del('lastLoginIp');
			$sess->del('lastLoginAt');
			$sess->del('lastInvLoginIp');
			$sess->del('lastInvLoginAt');
			
			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex $e) {
		}
	}
}
