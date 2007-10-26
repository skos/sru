<?
/**
 * akademik
 */
class UFdao_Sru_Dormitory
extends UFdao {

	public function listAll() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->id);

		return $this->doSelect($query);
	}
}
