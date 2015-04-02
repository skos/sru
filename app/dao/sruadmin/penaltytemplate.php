<?
/**
 * admin
 */
class UFdao_SruAdmin_PenaltyTemplate
extends UFdao {

	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->active, true);
		$query->order($mapping->title, $query->ASC);
		
		return $this->doSelect($query);
	}

	public function listInactive() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->active, false);
		$query->order($mapping->title, $query->ASC);
		
		return $this->doSelect($query);
	}

	public function getByTitle($title) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->title, $title);

		return $this->doSelectFirst($query);
	}
}
