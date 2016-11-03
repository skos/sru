<?
/**
 * sprawdzanie uprawnien administratora
 */
class UFacl_SruAdmin_Admin
extends UFlib_ClassWithService {

    const ASI = 0;
	const CENTRAL = 1;
	const CAMPUS = 2;
	const LOCAL = 3;
	const BOT = 4;
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('authAdmin');
	}
	
	public function login() {
		return !$this->_loggedIn();
	}

	//tylko administratorzy centralni i osiedlowi moga zarzadzac administratorami
		
	public function add() {
		$sess = $this->_srv->get('session');
		
		if($this->_loggedIn() && $sess->is('typeId') && ($sess->typeId == self::ASI || $sess->typeId == self::CENTRAL || $sess->typeId == self::CAMPUS) )
		{
			return true;
		}
		return false;
	}
	
	public function addChangeActiveDate(){
		$sess = $this->_srv->get('session');
		
		if($this->_loggedIn() && $sess->is('typeId') && ($sess->typeId == self::ASI || $sess->typeId == self::CENTRAL)){
			return true;
		}else{
			return false;
		}
	}
	
	public function changeUsersAndHostsDisplay($id){
		$sess = $this->_srv->get('session');
		
		if($this->_loggedIn() && ($id == $sess->authAdmin))
			return true;
		else
			return false;
	}

	public function changeAdminDorms($id) {
		$sess = $this->_srv->get('session');

		$bean = UFra::factory('UFbean_SruAdmin_Admin');
		$bean->getByPK($id);
		
		if($this->_loggedIn() && $sess->is('typeId') && ($sess->typeId == self::ASI || $sess->typeId == self::CAMPUS || $sess->typeId == self::CENTRAL) && ($bean->typeId == self::ASI || $bean->typeId == self::CENTRAL || $bean->typeId == self::CAMPUS || $bean->typeId == self::LOCAL)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function edit($id) {
		$sess = $this->_srv->get('session');
		
		if($this->_loggedIn() && $id == $sess->authAdmin) { //swoje konto kazdy moze edytowac
			return true;	
		}

		$bean = UFra::factory('UFbean_SruAdmin_Admin');
		$bean->getByPK($id);
		if($this->_loggedIn() && $sess->is('typeId') && ($sess->typeId == self::ASI ||$sess->typeId == self::CENTRAL || $sess->typeId == self::CAMPUS) &&
			($bean->typeId == self::ASI || $bean->typeId == self::CENTRAL || $bean->typeId == self::CAMPUS || $bean->typeId == self::LOCAL || $bean->typeId == self::BOT)) {
			return true;
		}
		return false;
	}	
	public function advancedEdit() {//do zmiany uprawnien i aktywnosci

		$sess = $this->_srv->get('session');
		
		if($this->_loggedIn() && $sess->is('typeId') && ($sess->typeId == self::ASI || $sess->typeId == self::CENTRAL || $sess->typeId == self::CAMPUS) )
		{
			return true;
		}
		return false;
	}			
	public function logout() {
		return $this->_loggedIn();
	}
}
