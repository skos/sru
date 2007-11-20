<?
/**
 * sprawdzanie uprawnien administratora
 */
class UFacl_SruAdmin_Admin
extends UFlib_ClassWithService {
	
	//@TODO: to moze byc tu?
	const 	CENTRAL		= 1,
			OSIEDLOWY	= 2, //@todo: a po angielsku??:p
			LOCAL		= 3,
			BOT			= 4;	
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('authAdmin');
	}
	
	public function login() {
		return !$this->_loggedIn();
	}
	public function add() {
		//tylko administrator centralny moze dodawac
		
		$sess = $this->_srv->get('session');
		
		if($this->_loggedIn() && $sess->is('typeId') && $sess->typeId == self::CENTRAL  )
		{
			return true;
		}
		return false;
	}	
	public function edit($id) {

		$sess = $this->_srv->get('session');
		
		if($this->_loggedIn() && $id == $sess->authAdmin) //swoje konto kazdy moze edytowac
		{
			return true;	
		}	
		if($this->_loggedIn() && $sess->is('typeId') && $sess->typeId == self::CENTRAL  )
		{
			return true;
		}
		return false;
	}	
	public function advancedEdit() {//do zmiany uprawnien i aktywnosci

		$sess = $this->_srv->get('session');
		
		if($this->_loggedIn() && $sess->is('typeId') && $sess->typeId == self::CENTRAL  )
		{
			return true;
		}
		return false;
	}			
	public function logout() {
		return $this->_loggedIn();
	}
}
