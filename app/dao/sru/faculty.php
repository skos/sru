<?
/**
 * wydzial
 */
class UFdao_Sru_Faculty
extends UFdao {

	public function listAll() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->name);

		return $this->doSelect($query);
	}
}
