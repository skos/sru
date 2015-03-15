<?
/**
 * admin
 */
class UFdao_SruAdmin_FwExceptions
extends UFdao {

	public function listWithActive($active = null) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		if (!is_null($active)) {
			$query->where($mapping->active, $active);
		}
		$query->order($mapping->host,  $query->ASC);
		$query->order($mapping->port,  $query->ASC);
			
		return $this->doSelect($query);
	}
}
