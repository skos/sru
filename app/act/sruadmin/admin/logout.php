<?

/**
 * wylogowanie administratora
 */
class UFact_SruAdmin_Admin_Logout
extends UFact {

	const PREFIX = 'adminLogout';

	public function go() {
		try {
			$this->_srv->get('session')->del('authAdmin');
			
			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex $e) {
		}
	}
}
