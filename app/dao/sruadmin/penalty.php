<?
/**
 * admin
 */
class UFdao_SruAdmin_Penalty
extends UFdao {

	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->active, true); 
		$query->order($mapping->endAt,  $query->ASC);
		
			
		return $this->doSelect($query);
	}

	public function listPast($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->active, true); 
		$query->where($mapping->endAt, NOW, $query->LT); 
		$query->limit($perPage+$overFetch);
		$query->offset($this->findOffset($page, $perPage));

		return $this->doSelect($query);
	}

	public function listAllByUserId($id, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->userId, $id);
		$query->order($mapping->endAt,  $query->DESC);
		
		return $this->doSelect($query);
	}

	public function listLastAdded($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->startAt, $query->DESC);
		$query->limit(10);
		
		return $this->doSelect($query);
	}

	public function listLastModified($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->modifiedAt, 0, $query->GTE);
		$query->order($mapping->modifiedAt,  $query->DESC);
		$query->limit(10);

		return $this->doSelect($query);
	}
	
	public function listLastAddedById($id, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->createdById, $id);
		$query->order($mapping->startAt, $query->DESC);
		$query->limit(10);
		
		return $this->doSelect($query);
	}

	public function listLastModifiedById($id, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->modifiedById, $id);
		$query->where($mapping->modifiedAt, 0, $query->GTE);
		$query->order($mapping->modifiedAt,  $query->DESC);
		$query->limit(10);

		return $this->doSelect($query);
	}

	public function listAllPenalties() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->endAt,  $query->ASC);
		
		return $this->doSelect($query);
	}
}
