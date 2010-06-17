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
			$serv = $this->_srv->get('req')->server;
			
			$login = $post['login'];
			$password = $post['password'];
			
			$password = UFbean_Sru_User::generatePassword($login, $password);
			$bean->getByLoginPassword($login, $password);

			if (is_null($bean->password2)) {
				$bean->password2 = UFbean_SruAdmin_Admin::generatePamPassword($password);
			}
			
			$sess = $this->_srv->get('session');
			$sess->authAdmin = $bean->id;
					
			$sess->name  		= $bean->name;
			$sess->typeId 		= $bean->typeId;
			$sess->lastLoginIp  = $bean->lastLoginIp;
			$sess->lastLoginAt  = $bean->lastLoginAt;
			
		
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
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
