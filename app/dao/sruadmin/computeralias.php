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

	public function getByDomainName($host) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->domainName, $host);

		return $this->doSelectFirst($query);
	}
	
	public function search($host) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$host = str_replace('%', '', $host);
		$host = str_replace('*', '%', $host);
		$query->where($mapping->host, $host, UFlib_Db_Query::LIKE);
		$query->order($mapping->host, $query->ASC);

		return $this->doSelect($query);
	}

	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->host, $query->ASC);

		return $this->doSelect($query);
	}
	
	public function listAllByDomain($domain=null) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		if (!is_null($domain)) {
			$query->where($mapping->domainSuffix, $domain);
		}
		$query->order($mapping->host, $query->ASC);

		return $this->doSelect($query);
	}
}
