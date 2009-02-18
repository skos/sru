<?
/**
 * admin
 */
class UFdao_SruAdmin_Admin
extends UFdao {

	public function getByLoginPassword($login, $password) {
		$key = $this->cachePrefix.'/'.__FUNCTION__.'/'.md5($login.'*#$%^@@!'.$password);
		try {
			return $this->cacheGet($key);
		} catch (UFex_Core_DataNotFound $e) {
			$mapping = $this->mapping('get');

			$query = $this->prepareSelect($mapping);
			$query->where($mapping->active, true);
			$query->where($mapping->login, $login);
			$query->where($mapping->password, $password);

			return $this->doSelectFirst($query);
		}
	}

	public function getByLogin($login) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->login, $login);

		return $this->doSelectFirst($query);
	}

	public function getFromSession() {
		return $this->getByPK($this->_srv->get('session')->authAdmin);
	}

	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		
		$query->where($mapping->active, true); 
		$query->where($mapping->typeId, UFacl_SruAdmin_Admin::BOT, UFlib_Db_Query::LT);		
		
		$query->order($mapping->dormitoryId, $query->ASC);	// @todo: kijowe rozwiazanie, ale jak bylo po aliasie, to "10" bylo przed "2"
		$query->order($mapping->typeId, $query->ASC);
		$query->order($mapping->name, $query->ASC);
		
		return $this->doSelect($query);
	}

	public function listAllInactive($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		
		$query->where($mapping->active, false);
		$query->where($mapping->typeId, UFacl_SruAdmin_Admin::BOT, UFlib_Db_Query::LT);
		
		$query->order($mapping->dormitoryId, $query->ASC);	// @todo: kijowe rozwiazanie, ale jak bylo po aliasie, to "10" bylo przed "2"
		$query->order($mapping->typeId, $query->ASC); //to czemus wadzi hrynek? wydaje mi sie ze najpierw wazniejsi powinny byc chociaz jak wolisz:P
		$query->order($mapping->name, $query->ASC);
		
		return $this->doSelect($query);
	}	

	public function listAllBots() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		
		$query->where($mapping->typeId, UFacl_SruAdmin_Admin::BOT); 
		
		$query->order($mapping->name, $query->ASC);
		
		return $this->doSelect($query);
	}	

	public function getFromHttp() {
		$server = $this->_srv->get('req')->server;
		$login = $server->PHP_AUTH_USER;
		$password = $server->PHP_AUTH_USER;
		$password = UFbean_Sru_User::generatePassword($login, $password);

		return $this->getByLoginPassword($login, $password);
	}

}
