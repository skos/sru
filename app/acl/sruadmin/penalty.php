<?php
/**
 * sprawdzanie uprawnien
 */
class UFacl_SruAdmin_Penalty
extends UFlib_ClassWithService {
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('authAdmin');
	}

	public function edit() {
		return $this->_loggedIn();
	}

	public function add() {
		return $this->_loggedIn();
	}

	public function del() {
		return $this->_loggedIn();
	}
	
	/**
	 * sprawdza uprawnienia do edycji danej kary
	 * 
	 * @param int $id - id kary
	 * @return bool
	 */
	public function editOne($id) {
		if (!$this->_loggedIn()) {
			return false;
		}
		$sess = $this->_srv->get('session');
		$bean = UFra::factory('UFbean_SruAdmin_Penalty');
		try {
			$bean->getByPK($id);
		} catch (Exception $e) {
			return false;
		}
		
		if ($sess->authAdmin == $bean->createdById) {	//swoje kary mozna edytowac
			return true;
		} elseif ($sess->typeId == UFacl_SruAdmin_Admin::CENTRAL || $sess->typeId == UFacl_SruAdmin_Admin::CAMPUS) {
			return true;
		} else {
			return ($bean->amnestyAfter<$bean->endAt);
		}
	}

	/**
	 * sprawdza uprawnienia do pelnej edycji danej kary
	 * 
	 * @param int $id - id kary
	 * @return bool
	 */
	public function editOneFull($id) {
		if (!$this->_loggedIn()) {
			return false;
		}
		$sess = $this->_srv->get('session');
		$bean = UFra::factory('UFbean_SruAdmin_Penalty');
		try {
			$bean->getByPK($id);
		} catch (Exception $e) {
			return false;
		}
		
		if ($sess->authAdmin == $bean->createdById) {	//swoje kary mozna edytowac
			return true;	
		} elseif ($sess->typeId == UFacl_SruAdmin_Admin::CENTRAL || $sess->typeId == UFacl_SruAdmin_Admin::CAMPUS) {
			return true;
		}
		return false;
	}
}
