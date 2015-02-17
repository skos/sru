<?
/**
 * akademik
 */
class UFdao_Sru_Dormitory
extends UFdao {

	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->active, true);
		$query->order($mapping->displayOrder);

		return $this->doSelect($query);
	}

	public function listAllForWalet() {
		$mapping = $this->mapping('listWalet');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->active, true);
		$query->order($mapping->displayOrder);

		return $this->doSelect($query);
	}

	public function getByAlias($alias) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->active, true);
		$query->where($mapping->alias, $alias);

		return $this->doSelectFirst($query);
	}	
}
