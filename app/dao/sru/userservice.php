<?
/**
 * usługi usera
 */
class UFdao_Sru_UserService
extends UFdao {

	public function listAllByUserId($id, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->userId, $id);
			
		return $this->doSelect($query);
	}
	
	/**
	 * Funkcja konstruująca zapytanie wyciągające 10 ostatnio zmodyfikowanych usług
	 * type = 1 dla poziomu admina, type = 2 dla poziomu usera
	 */
	public function listLastModified($id = null, $type = null, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('listDetails');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->modifiedAt, 0, $query->GTE);
		if($type == 1)
			$query->where('('.$mapping->column('type').'=2 OR '.$mapping->column('type').'=4)', null, $query->SQL);
		elseif($type == 2)
			$query->where('('.$mapping->column('type').'=1 OR '.$mapping->column('type').'=3)', null, $query->SQL);
		if (isset($id)) {
			$query->where($mapping->modifiedById, $id);
		}
		$query->order($mapping->modifiedAt,  $query->DESC);
		$query->limit(10);

		return $this->doSelect($query);
	}
}
