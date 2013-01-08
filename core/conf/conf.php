<?php

/**
 * konfiguracja
 */
class UFconf {

	public function __get($var) {
		if (!isset($this->$var)) {
			throw UFra::factory('UFex_Core_DataNotFound', $var);
		}
		return $this->$var;
	}

	public function __set($var, $val) {
		throw UFra::factory('UFex_Core_NoMethod', 'Setting not allowed');
	}
}
