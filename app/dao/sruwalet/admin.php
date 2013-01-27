<?
/**
 * admin Waleta
 */
class UFdao_SruWalet_Admin
extends UFdao {

	public function getByLoginToAuthenticate($login) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->active, true);
		$query->where($mapping->login, $login);
		$query->where($mapping->typeId, UFacl_SruWalet_Admin::DORM, UFlib_Db_Query::GTE);

		return $this->doSelectFirst($query);
	}

	public function getByLogin($login) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->login, $login);

		return $this->doSelectFirst($query);
	}

	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		
		$query->where($mapping->active, true); 
		$query->where($mapping->typeId, UFacl_SruWalet_Admin::DORM, UFlib_Db_Query::GTE);
		
		$query->order($mapping->name, $query->ASC);
		
		return $this->doSelect($query);
	}

	public function listAllInactive($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		
		$query->where($mapping->active, false);
		$query->where($mapping->typeId, UFacl_SruWalet_Admin::DORM, UFlib_Db_Query::GTE);
		
		$query->order($mapping->typeId, $query->ASC);
		$query->order($mapping->name, $query->ASC);
		
		return $this->doSelect($query);
	}

	public function getFromSession() {
		return $this->getByPK($this->_srv->get('session')->authWaletAdmin);
	}
}
