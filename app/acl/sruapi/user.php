<?php
/**
 * sprawdzanie uprawnien przy wywolaniach przez api
 */
class UFacl_SruApi_User
extends UFacl_SruApi {

	public function show() {
		return $this->_loggedIn();
	}

	public function edit() {
		return $this->_loggedIn();
	}
}
