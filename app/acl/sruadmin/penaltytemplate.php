<?php
/**
 * sprawdzanie uprawnien
 */
class UFacl_SruAdmin_PenaltyTemplate
extends UFlib_ClassWithService {
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('authAdmin');
	}

	public function edit() {
		if (!$this->_loggedIn()) {
			return false;
		}
		$sess = $this->_srv->get('session');		
		if ($sess->typeId == UFacl_SruAdmin_Admin::CENTRAL || $sess->typeId == UFacl_SruAdmin_Admin::CAMPUS) {
			return true;
		}
		return false;
	}

	public function add() {
		if (!$this->_loggedIn()) {
			return false;
		}
		$sess = $this->_srv->get('session');		
		if ($sess->typeId == UFacl_SruAdmin_Admin::CENTRAL || $sess->typeId == UFacl_SruAdmin_Admin::CAMPUS) {
			return true;
		}
		return false;
	}
}
