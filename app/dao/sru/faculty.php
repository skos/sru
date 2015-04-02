<?
/**
 * wydzial
 */
class UFdao_Sru_Faculty
extends UFdao {

	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->name);

		return $this->doSelect($query);
	}
}
