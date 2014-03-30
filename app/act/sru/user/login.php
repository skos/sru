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
			$serv = $this->_srv->get('req')->server;

			$login = $post['login'];
			$password = $post['password'];
			$password = UFbean_Sru_User::generatePassword($login, $password);
			$bean->getByLoginPassword($login, $password);
			
			$sess = $this->_srv->get('session');
			$sess->auth = $bean->id;
			$sess->lastLoginIp  = $bean->lastLoginIp;
			$sess->lastLoginAt  = $bean->lastLoginAt;
			$sess->lastInvLoginIp  = $bean->lastInvLoginIp;
			$sess->lastInvLoginAt  = $bean->lastInvLoginAt;
                        $sess->lang=$bean->lang;
			if ($_SERVER['SERVER_PORT'] != '443') {
				$sess->secureConnection  = false;
			} else {
				$sess->secureConnection  = true;
			}
			
			if($serv->is('HTTP_X_FORWARDED_FOR') && $serv->HTTP_X_FORWARDED_FOR != '' ) {
				$bean->lastLoginIp = $serv->HTTP_X_FORWARDED_FOR;
			} else {
				$bean->lastLoginIp =  $serv->REMOTE_ADDR;
			}
			$bean->lastLoginAt = NOW;
			$bean->save();
			
			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex_Dao_NotFound $e) {
			$this->markErrors(self::PREFIX, array('login'=>'notAuthorized'));
			try {
				$bean = UFra::factory('UFbean_Sru_User');
				$login = $post['login'];
				$bean->getByLogin($login);
				if($serv->is('HTTP_X_FORWARDED_FOR') && $serv->HTTP_X_FORWARDED_FOR != '' ) {
					$bean->lastInvLoginIp = $serv->HTTP_X_FORWARDED_FOR;
				} else {
					$bean->lastInvLoginIp =  $serv->REMOTE_ADDR;
				}
				$bean->lastInvLoginAt = NOW;
				$bean->save();
			} catch (UFex_Dao_NotFound $e) {
				// nie ma komu zapisac info o blednym logowaniu
			}
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
