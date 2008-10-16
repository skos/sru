<?

/**
 * zalogowanie uzytkownika
 */
class UFact_Sru_User_Login
extends UFact {

	const PREFIX = 'userLogin';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Sru_User');
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$login = $post['login'];
			$password = $post['password'];
			$password = UFbean_Sru_User::generatePassword($login, $password);
			$bean->getByLoginPassword($login, $password);
			


			$sess = $this->_srv->get('session');
			$sess->auth = $bean->id;
			
			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex_Dao_NotFound $e) {
			$this->markErrors(self::PREFIX, array('login'=>'notAuthorized'));
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
