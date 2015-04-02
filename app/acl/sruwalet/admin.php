<?
/**
 * sprawdzanie uprawnien administratora Waleta
 */
class UFacl_SruWalet_Admin
extends UFlib_ClassWithService {
	
	const DORM = 11;
	const OFFICE = 12;
	const HEAD = 13;
	const PORTIER = 14;
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('authWaletAdmin');
	}
	
	public function login() {
		return !$this->_loggedIn();
	}

	//tylko kierownictwo moze zarzadzac administratorami Waleta
	public function add() {
		$sess = $this->_srv->get('session');
		
		if($this->_loggedIn() && $sess->is('typeIdWalet') && $sess->typeIdWalet == self::HEAD ) {
			return true;
		}
		return false;
	}

	public function edit($id) {
		$sess = $this->_srv->get('session');
		
		if($this->_loggedIn() && $id == $sess->authWaletAdmin) { //swoje konto kazdy moze edytowac za wyjątkiem portiera
			if ($sess->typeIdWalet == self::PORTIER) {
				return false;
			}
			return true;	
		}

		$bean = UFra::factory('UFbean_SruWalet_Admin');
		$bean->getByPK($id);
		if($this->_loggedIn() && $sess->is('typeIdWalet') && $sess->typeIdWalet == self::HEAD &&
			($bean->typeId == self::DORM || $bean->typeId == self::OFFICE || $bean->typeId == self::HEAD || $bean->typeId == self::PORTIER)) {
			return true;
		}
		return false;
	}
	
	public function view() {
		$sess = $this->_srv->get('session');
		if ($sess->is('typeIdWalet') && $sess->typeIdWalet == UFacl_SruWalet_Admin::PORTIER) {
			return false;
		}
		return true;
	}
	
	public function toDoListView() {
		$sess = $this->_srv->get('session');
		if ($sess->typeIdWalet == UFacl_SruWalet_Admin::PORTIER) {
			return false;
		}
		return true;
	}

	public function advancedEdit() {//do zmiany uprawnien i aktywnosci

		$sess = $this->_srv->get('session');
		
		if($this->_loggedIn() && $sess->is('typeIdWalet') && $sess->typeIdWalet == self::HEAD) {
			return true;
		}
		return false;
	}

	public function logout() {
		return $this->_loggedIn();
	}
	
	public function addChangeActiveDate(){
		$sess = $this->_srv->get('session');
		
		if ($this->_loggedIn() && $sess->is('typeIdWalet') && $sess->typeIdWalet == self::HEAD) {
			return true;
		} else{
			return false;
		}
	}
}
