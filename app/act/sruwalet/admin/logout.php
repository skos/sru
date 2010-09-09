<?

/**
 * wylogowanie administratora Waleta
 */
class UFact_SruWalet_Admin_Logout
extends UFact {

	const PREFIX = 'adminLogout';

	public function go() {
		try {
			$sess = $this->_srv->get('session');
			$sess->del('authWaletAdmin');
			$sess->del('nameWalet');
			$sess->del('typeIdWalet');
			$sess->del('lastLoginIpWalet');
			$sess->del('lastLoginAtWalet');
			
			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex $e) {
		}
	}
}
