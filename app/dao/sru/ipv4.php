<?
/**
 * ip
 */
class UFdao_Sru_Ipv4
extends UFdao {

	public function getFreeByDormitoryId($dormitory) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->dormitoryId, $dormitory);
		$query->where($mapping->host, null);
		$query->where('(((SELECT modified_at FROM computers_history h where h.ipv4=i.ip limit 1) IS NULL) OR (SELECT modified_at FROM computers_history h where h.ipv4=i.ip order by modified_at desc limit 1) < (TIMESTAMP \'NOW\' - TIME \'01:00\'))', null ,$query->SQL);

		return $this->doSelectFirst($query);
	}

	public function getByIp($ip) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->ip, $ip);

		return $this->doSelectFirst($query);
	}

	public function getFreeByIp($ip) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->ip, $ip);
		$query->where($mapping->host, null);

		return $this->doSelectFirst($query);
	}
}
