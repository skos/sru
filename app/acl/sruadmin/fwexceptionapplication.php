<?
/**
 * sprawdzanie uprawnien wniosku o uslugi serwerowe
 */
class UFacl_SruAdmin_FwExceptionApplication
extends UFlib_ClassWithService {
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('authAdmin');
	}

	public function add() {
		return $this->_loggedIn();
	}
	
	public function edit($appId) {
		$sess = $this->_srv->get('session');
		if($this->_loggedIn() && $sess->is('typeId') && ($sess->typeId == UFacl_SruAdmin_Admin::CENTRAL || $sess->typeId == UFacl_SruAdmin_Admin::CAMPUS)) {
			try {
				$app = UFra::factory('UFbean_Sru_FwExceptionApplication');
				$app->getByPK($appId);
				if (is_null($app->skosOpinion) && $app->validTo > NOW) {
					return true;
				}
			} catch (UFex_Dao_NotFound $e) {
				return false;
			}
		}
		return false;
	}
}
