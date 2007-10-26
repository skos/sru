<?
/**
 * pokoj
 */
class UFdao_Sru_Location
extends UFdao {

	public function getByAliasDormitory($alias, $dormitoryId) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->dormitoryId, $dormitoryId);
		$query->where($mapping->alias, strtolower($alias));

		return $this->doSelectFirst($query);
	}
}
