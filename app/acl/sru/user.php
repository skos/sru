<?
/**
 * sprawdzanie uprawnien
 */
class UFacl_Sru_User
extends UFlib_ClassWithService {
	
	public function edit() {
		return true;
	}

	public function add() {
		return true;
	}
	
	public function del() {
		return true;
	}
}
