<?php
/**
 * skladuje dane sesji
 */
class UFlib_Session
extends UFlib_Datastorage {

	/**
	 * konstruktor
	 *
	 * @param array - tablica (nazwa => wartosc) z danymi poczatkowymi
	 */
	public function __construct() {
		session_start();
		parent::__construct($_SESSION);
		unset($_SESSION);
	}
}
