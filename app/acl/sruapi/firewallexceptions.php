<?php
/**
 * sprawdzanie uprawnien przy wyowalniach przez api
 */
class UFacl_SruApi_Firewallexceptions
extends UFacl_SruApi {

	public function edit() {
		return $this->_loggedIn();
	}
}
