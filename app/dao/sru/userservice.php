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
	 * Funkcja konstruująca zapytanie wyciągające 10 ostatnio zmodyfikowanych usług.
	 *
	 */
	public function listLastModified($id = null, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('listDetails');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->modifiedAt, 0, $query->GTE);
		if (isset($id)) {
			$query->where($mapping->modifiedById, $id);
		}
		$query->order($mapping->modifiedAt,  $query->DESC);
		$query->limit(10);

		return $this->doSelect($query);
	}
	
	/**
	 * Funkcja konstruująca zapytanie wyciągające 10 ostatnio dodanych usług.
	 *
	 */
	public function listLastAdded($id = null, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('listDetails');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->modifiedAt, 0, $query->GTE);
		$query->where($mapping->type, 1, $query->EQ);
		if (isset($id)) {
			$query->where($mapping->modifiedById, $id);
		}
		$query->order($mapping->modifiedAt,  $query->DESC);
		$query->limit(10);

		return $this->doSelect($query);
	}
}
