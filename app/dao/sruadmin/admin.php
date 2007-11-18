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
		
		$query->where($mapping->active, true); 
		
		$query->order($mapping->dormitoryId, $query->ASC);	// @todo: kijowe rozwiazanie, ale jak bylo po aliasie, to "10" bylo przed "2"
		$query->order($mapping->typeId, $query->ASC); //to czemus wadzi hrynek? wydaje mi sie ze najpierw wazniejsi powinny byc chociaz jak wolisz:P
		$query->order($mapping->name, $query->ASC);
		
		return $this->doSelect($query);
	}
	public function listAllInactive() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		
		$query->where($mapping->active, false);
		
		$query->order($mapping->dormitoryId, $query->ASC);	// @todo: kijowe rozwiazanie, ale jak bylo po aliasie, to "10" bylo przed "2"
		$query->order($mapping->typeId, $query->ASC); //to czemus wadzi hrynek? wydaje mi sie ze najpierw wazniejsi powinny byc chociaz jak wolisz:P
		$query->order($mapping->name, $query->ASC);
		
		return $this->doSelect($query);
	}	
	public function listAllBots() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		
		$query->where($mapping->typeId, 4);  //@todo: to zawsze bedzie '4'?:P
		
		$query->order($mapping->name, $query->ASC);
		
		return $this->doSelect($query);
	}	
}
