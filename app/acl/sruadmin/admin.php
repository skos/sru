<?
/**
 * sprawdzanie uprawnien administratora
 */
class UFacl_SruAdmin_Admin
extends UFlib_ClassWithService {
	
	const 	CENTRAL		= 1,
			CAMPUS		= 2,
			LOCAL		= 3,
			BOT			= 4;	
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('authAdmin');
	}
	
	public function login() {
		return !$this->_loggedIn();
	}

	//tylko administratorzy centralni i osiedlowi moga zarzadzac administratorami
		
	public function add() {
		$sess = $this->_srv->get('session');
		
		if($this->_loggedIn() && $sess->is('typeId') && ($sess->typeId == self::CENTRAL || $sess->typeId == self::CAMPUS) )
		{
			return true;
		}
		return false;
	}	
	public function edit($id) {
		$sess = $this->_srv->get('session');
		
		if($this->_loggedIn() && $id == $sess->authAdmin) { //swoje konto kazdy moze edytowac
			return true;	
		}

		$bean = UFra::factory('UFbean_SruAdmin_Admin');
		$bean->getByPK($id);
		if($this->_loggedIn() && $sess->is('typeId') && ($sess->typeId == self::CENTRAL || $sess->typeId == self::CAMPUS) &&
			($bean->typeId == self::CENTRAL || $bean->typeId == self::CAMPUS || $bean->typeId == self::LOCAL || $bean->typeId == self::BOT)) {
			return true;
		}
		return false;
	}	
	public function advancedEdit() {//do zmiany uprawnien i aktywnosci

		$sess = $this->_srv->get('session');
		
		if($this->_loggedIn() && $sess->is('typeId') && ($sess->typeId == self::CENTRAL || $sess->typeId == self::CAMPUS) )
		{
			return true;
		}
		return false;
	}			
	public function logout() {
		return $this->_loggedIn();
	}
}
