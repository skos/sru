<?
/**
 * sprawdzanie uprawnien
 */
class UFacl_SruWalet_Dorm
extends UFlib_ClassWithService {
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('authWaletAdmin');
	}

	public function view($dormitory) {
		if (!$this->_loggedIn()) {
			return false;
		}

		$sess = $this->_srv->get('session');
		if ($sess->typeIdWalet == UFacl_SruWalet_Admin::HEAD || $sess->typeIdWalet == UFacl_SruWalet_Admin::OFFICE || $sess->typeIdWalet == UFacl_SruWalet_Admin::PORTIER) {
			return true;
		}

		try {
			$admDorm = UFra::factory('UFbean_SruWalet_AdminDormitoryList');
			$admDorm->listAllById($sess->authWaletAdmin);
		} catch (UFex_Dao_NotFound $e) {
			return false;
		}
		foreach ($admDorm as $dorm) {
			if ($dorm['dormitoryAlias'] == $dormitory) {
				return true;
			}
		}
		return false;
	}
                
        public function checkOut($dormitory) {//uprawnienie do wymeldowania wszystkich userow
                if (!$this->_loggedIn()) {
			return false;
		}
                
                $sess = $this->_srv->get('session');
		if ($sess->typeIdWalet == UFacl_SruWalet_Admin::HEAD) {
                    return true;
                } else if ($sess->typeIdWalet == UFacl_SruWalet_Admin::DORM) {
			try {
                            $admDorm = UFra::factory('UFbean_SruWalet_AdminDormitoryList');
                            $admDorm->listAllById($sess->authWaletAdmin);
                        } catch (UFex_Dao_NotFound $e) {
                            return false;
                        }
                        foreach ($admDorm as $dorm) {
                            if ($dorm['dormitoryAlias'] == $dormitory) {
				return true;
                            }
                        }
		}
		return false;
	}
}
