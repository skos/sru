<?
/**
 * sprawdzanie uprawnien
 */
class UFacl_SruWalet_UserFunction
extends UFlib_ClassWithService {
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('authWaletAdmin');
	}

	public function edit($functionId) {
		if (!$this->_loggedIn()) {
			return false;
		}

		$sess = $this->_srv->get('session');
		if ($sess->typeIdWalet == UFacl_SruWalet_Admin::HEAD) {
			return true;
		}
		if ($sess->typeIdWalet == UFacl_SruWalet_Admin::PORTIER) {
			return false;
		}

		if ($functionId != UFbean_Sru_UserFunction::TYPE_CAMPUS_CHAIRMAN) {
			return true;
		}
		return false;
	}

}
