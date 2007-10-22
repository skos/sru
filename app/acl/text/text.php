<?
/**
 * sprawdzanie uprawnien
 */
class UFacl_Text_Text
extends UFlib_ClassWithService {
	
	public function edit() {
		return true;
	}

	public function add() {
		return $this->edit();
	}
	
	public function del() {
		return $this->edit();
	}
}
