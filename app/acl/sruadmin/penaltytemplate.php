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
		return $this->_loggedIn();
	}

	public function add() {
		return $this->_loggedIn();
	}
}
