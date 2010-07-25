<?
/**
 * aliasy komputera
 */
class UFdao_SruAdmin_ComputerAlias
extends UFdao {

	public function listByComputerId($id) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->computerId, $id);
		$query->order($mapping->host, $query->ASC);

		return $this->doSelect($query);
	}

	public function getByHost($host) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->host, $host);

		return $this->doSelectFirst($query);
	}
}
