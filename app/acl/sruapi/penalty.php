<?php
/**
 * sprawdzanie uprawnien przy wyowalniach przez api
 */
class UFacl_SruApi_Penalty
extends UFacl_SruApi {

	public function show() {
		return $this->_loggedIn();
	}

	public function amnesty() {
		return $this->_loggedIn();
	}
}
