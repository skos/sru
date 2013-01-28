<?
/**
 * zalogowanie admina
 */
class UFact_SruAdmin_Admin_Login
extends UFact {

	const PREFIX = 'adminLogin';
	private $maxBadLoginsCount = 2;

	public function go() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_Admin');
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$serv = $this->_srv->get('req')->server;
			
			$login = $post['login'];
			$password = $post['password'];
			
			$bean->getByLoginToAuthenticate($login);
			if (!UFbean_SruAdmin_Admin::validateBlowfishPassword($password, $bean->password)) {
				throw UFra::factory('UFex_Dao_NotFound', 'Incorrect login or password', 0, E_ERROR);
			}
			
			if($bean->badLogins > 0){
			    $bean->badLogins -= 1;
			    $bean->save();
			    $this->markOk(self::PREFIX);
			    if($bean->badLogins > 0){
				return ;
			    }
			}
			
			$sess = $this->_srv->get('session');
			$sess->authAdmin = $bean->id;
					
			$sess->name  = $bean->name;
			$sess->typeId = $bean->typeId;
			$sess->lastLoginIpAdmin = $bean->lastLoginIp;
			$sess->lastLoginAtAdmin = $bean->lastLoginAt;
			$sess->lastInvLoginIpAdmin  = $bean->lastInvLoginIp;
			$sess->lastInvLoginAtAdmin  = $bean->lastInvLoginAt;
		
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
				$bean = UFra::factory('UFbean_SruAdmin_Admin');
				$login = $post['login'];
				$bean->getByLogin($login);
				if($bean->badLogins < $this->maxBadLoginsCount){
				    $bean->badLogins += 1;
				}
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
