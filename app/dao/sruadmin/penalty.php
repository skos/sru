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

	public function listLastModified($type = null, $id = null, $limit = 10, $timeLimit = null) {
		$mapping = $this->mapping('listDetails');
		
		$modBy = "WHERE modified_by is not null";
		$timeCondition = "";
		$order = "";
		
		if (isset($id)) {
			$modBy = "WHERE modified_by=" . $id;
		}
		
		if(isset($timeLimit)){
			$timeCondition = "AND EXTRACT (EPOCH FROM modifieda) >= " . (time() - $timeLimit);
			$order = "ORDER BY modifiedat ASC";
		}else{
			$order = "ORDER BY modifiedat DESC LIMIT " . $limit;
		}
		
		$query = $this->prepareSelect($mapping);

		$query->raw("SELECT * FROM (
					SELECT DISTINCT ON (foo.id) foo.id, EXTRACT (EPOCH FROM max(modifieda)) AS modifiedat, typeid, 
					userid, u.name, surname, u.login, banned, u.active, endat, template, modifiedby, a.name AS modifiername,
					(SELECT count(*) FROM penalties_history h WHERE h.penalty_id = foo.id) AS modificationcount,
					d.alias AS userdormalias
					FROM
					(SELECT id, user_id AS userid, p.modified_at AS modifieda, type_id AS typeid, end_at AS endat,
						(SELECT title FROM penalty_templates t WHERE template_id = t.id) AS template,
						p.modified_by AS modifiedby
					FROM penalties p " . $modBy . "
					UNION SELECT penalty_id AS id, (SELECT user_id FROM penalties WHERE penalty_id = id) AS userid, 
					h.modified_at AS modifieda, (SELECT type_id FROM penalties WHERE penalty_id = id) AS typeid,
					end_at AS endat, 
					(SELECT title FROM penalties p, penalty_templates t WHERE penalty_id = p.id 
						AND p.template_id = t.id) AS template, modified_by AS modifiedby
					FROM penalties_history h " . $modBy . ")
					AS foo LEFT JOIN users u ON u.id = userid
					LEFT JOIN admins a ON modifiedby = a.id
					LEFT JOIN locations l ON l.id = u.location_id
					LEFT JOIN dormitories d ON d.id = l.dormitory_id
					WHERE u.modified_at is not null " . $timeCondition
					. " GROUP BY foo.id, userid, u.name, surname, u.login, banned, u.active, typeid, endat, template, 
						modifiedby, a.name, d.alias
					ORDER BY foo.id, modifiedat DESC 
					) AS foo2 " . $order . ";");

		return $this->doSelect($query);
	}

	public function listAllPenalties() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->endAt,  $query->ASC);
		
		return $this->doSelect($query);
	}

	public function listActiveByLocationId($locationId) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->active, true);
		$query->where($mapping->userActive, true);
		$query->where($mapping->userLocationId, $locationId);
		$query->order($mapping->endAt,  $query->ASC);

		return $this->doSelect($query);
	}
	
	public function getAllActiveByUserId($userId) {
		$mapping = $this->mapping('list');
		
		$query = $this->prepareSelect($mapping);
		$query->where($mapping->userId, $userId);
		$query->where($mapping->active, true);
		
		return $this->doSelect($query);
	}
}
