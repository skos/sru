<?
/**
 * sprawdzanie uprawnien do sprzetu
 */
class UFacl_SruWalet_Inventory
extends UFlib_ClassWithService {
	public function view() {
		$sess = $this->_srv->get('session');
		if ($sess->is('typeIdWalet') && $sess->typeIdWalet == UFacl_SruWalet_Admin::PORTIER) {
			return false;
		}
		return true;
	}
}
