<?

/**
 * wylogowanie administratora
 */
class UFact_SruAdmin_Admin_Logout
extends UFact {

	const PREFIX = 'adminLogout';

	public function go() {
		try {
			$sess = $this->_srv->get('session');
			$sess->del('authAdmin');
			$sess->del('name');
			$sess->del('typeId');			
			$sess->del('lastLoginIp');
			$sess->del('lastLoginAt');			
			
			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex $e) {
		}
	}
}
