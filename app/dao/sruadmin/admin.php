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
		
		$query->where($mapping->active, true); // @todo: chyba jednak lepiej wszystkich zlistowac?
		                                       // nie, na nich bedzie osobny box, bo do normalnej pracy nie sa potrzebni [hrynek]
		
		$query->order($mapping->dormitoryId, $query->ASC);	// @todo: kijowe rozwiazanie, ale jak bylo po aliasie, to "10" bylo przed "2"
		$query->order($mapping->name, $query->ASC);
		
		return $this->doSelect($query);
	}
}
