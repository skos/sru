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

	public function listLastAdded($type = null, $id = null, $limit = 10, $timeLimit = null, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		if (isset($type)) {
			if ($type == 1) {
				$query->where($mapping->typeId, 1);
			} else {
				$query->where($mapping->typeId, 1, $query->NOT_EQ);
			}
		}
		if (isset($id)) {
			$query->where($mapping->createdById, $id);
		}
		if (isset($timeLimit)) {
			$query->where($mapping->createdAt, time() - $timeLimit, $query->GTE);
			$query->order($mapping->startAt,  $query->ASC);
		} else {
			$query->order($mapping->startAt,  $query->DESC);
		}
		if (isset($limit)) {
			$query->limit(10);
		}
		
		return $this->doSelect($query);
	}

	public function listLastModified($type = null, $id = null, $limit = 10, $timeLimit = null, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('listDetails');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->modifiedAt, 0, $query->GTE);
		if (isset($id)) {
			$query->where($mapping->modifiedById, $id);
		}
		if (isset($type)) {
			$query->where($mapping->typeId, 1, $query->NOT_EQ);
		}
		if (isset($timeLimit)) {
			$query->where($mapping->modifiedAt, time() - $timeLimit, $query->GTE);
			$query->order($mapping->modifiedAt,  $query->ASC);
		} else {
			$query->order($mapping->modifiedAt,  $query->DESC);
		}
		if (isset($limit)) {
			$query->limit(10);
		}

		return $this->doSelect($query);
	}

	public function listAllPenalties() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->endAt,  $query->ASC);
		
		return $this->doSelect($query);
	}
}
