<?
/**
 * sprawdzanie uprawnien do obslugi pokoju
 */
class UFacl_SruWalet_Room
extends UFlib_ClassWithService {
		
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('authWaletAdmin');
	}

	//tylko kierownictwo moze zarzadzac pokojami
	public function edit() {
		$sess = $this->_srv->get('session');
		
		if($this->_loggedIn() && $sess->is('typeIdWalet') && $sess->typeIdWalet == UFacl_SruWalet_Admin::HEAD) {
			return true;
		}
		return false;
	}
}
