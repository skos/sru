<?
/**
 * zalogowanie admina Waleta
 */
class UFact_SruWalet_Admin_Login
extends UFact {

	const PREFIX = 'adminLogin';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_SruWalet_Admin');
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$serv = $this->_srv->get('req')->server;
			
			$login = $post['login'];
			$password = $post['password'];
			
			$password = UFbean_SruAdmin_Admin::generatePassword($password);
			$bean->getByLoginPassword($login, $password);
			
			$sess = $this->_srv->get('session');
			$sess->authWaletAdmin = $bean->id;
					
			$sess->nameWalet  = $bean->name;
			$sess->typeIdWalet = $bean->typeId;
			$sess->lastLoginIpWalet  = $bean->lastLoginIp;
			$sess->lastLoginAtWalet  = $bean->lastLoginAt;
			
		
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
