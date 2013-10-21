<?
/**
 * sprawdzanie uprawnien do narodowości
 */
class UFacl_SruWalet_Country
extends UFlib_ClassWithService {
	
	public function view() {
		$sess = $this->_srv->get('session');
		if ($sess->typeIdWalet == UFacl_SruWalet_Admin::PORTIER) {
			return false;
		}
		return true;
	}

}
