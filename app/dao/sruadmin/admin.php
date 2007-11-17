<?
/**
 * admin
 */
class UFdao_SruAdmin_Admin
extends UFdao {

	public function getByLoginPassword($login, $password) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->login, $login);
		$query->where($mapping->password, $password);

		return $this->doSelectFirst($query);
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
	public function listAll() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		
//		$query->where($mapping->active, true); // @todo: chyba jednak lepiej wszystkich zlistowac?
		
		$query->order($mapping->dormitoryAlias, $query->ASC);
		$query->order($mapping->typeId, $query->ASC);
		$query->order($mapping->active, $query->DESC);
		
		return $this->doSelect($query);
	}
}
