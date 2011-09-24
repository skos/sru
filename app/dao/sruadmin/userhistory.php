<?
/**
 * historia uzytkownika
 */
class UFdao_SruAdmin_UserHistory
extends UFdao {

	public function listByUserIdPK($user, $id) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->userId, $user);
		$query->where($mapping->pkName(), $id);

		return $this->doSelect($query);
	}

	public function listByUserId($id, $count=null) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->userId, $id);
		$query->order($mapping->pkName(), $query->DESC);
		if (is_int($count)) {
			$query->limit($count);
		}

		return $this->doSelect($query);
	}
}
