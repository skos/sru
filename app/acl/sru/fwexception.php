<?
/**
 * sprawdzanie uprawnien
 */
class UFacl_Sru_FwException
extends UFlib_ClassWithService {
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('auth');
	}

	public function edit() {
		if (!$this->_loggedIn()) {
			return false;
		}
		$userId = $this->_srv->get('session')->auth;
		
		try {
			$functions = UFra::factory('UFbean_Sru_UserFunctionList');
			$functions->listByUserId($userId);
			foreach ($functions as $func) {
				if ($func['functionId'] == UFbean_Sru_UserFunction::TYPE_CAMPUS_CHAIRMAN) {
					return true;
				}
			}
		} catch (UFex_Dao_NotFound $e) {
			return false;
		}
		
		return false;
	}
	
	public function editApp($appId) {
		if (!$this->edit()) {
			return false;
		}
		try {
			$app = UFra::factory('UFbean_Sru_FwExceptionApplication');
			$app->getByPK($appId);
			if (!is_null($app->skosOpinion) && $app->skosOpinion == true && is_null($app->sspgOpinion) && $app->validTo > NOW) {
				return true;
			}
		} catch (UFex_Dao_NotFound $e) {
			return false;
		}
		return false;
	}
}
