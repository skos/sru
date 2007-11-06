<?

/**
 * zalogowanie admina
 */
class UFact_SruAdmin_Admin_Login
extends UFact {

	const PREFIX = 'adminLogin';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_Admin');
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$login = $post['login'];
			$password = md5($post['login'].$post['password']);
			$bean->getByLoginPassword($login, $password);

			$sess = $this->_srv->get('session');
			$sess->authAdmin = $bean->id;
			
			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex_Dao_NotFound $e) {
			$this->markErrors(self::PREFIX, array('login'=>'notAuthorized'));
		}
	}
}
