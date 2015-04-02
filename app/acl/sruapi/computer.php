<?php
/**
 * sprawdzanie uprawnien przy wyowalniach przez api
 */
class UFacl_SruApi_Computer
extends UFacl_SruApi {

	public function showLocations() {
		return $this->_loggedIn();
	}

	public function show() {
		return $this->_loggedIn();
	}

	public function edit() {
		return $this->_loggedIn();
	}
}
