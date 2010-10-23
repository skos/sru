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

	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->host, $query->ASC);

		return $this->doSelect($query);
	}
}
