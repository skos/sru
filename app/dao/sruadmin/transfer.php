<?
/**
 * transfer
 */
class UFdao_SruAdmin_Transfer
extends UFdao {

	public function listTop() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where('l.time > now() - interval \'30 minutes\' GROUP BY l.ip, c.can_admin, c.type_id, c.host, c.id, c.banned HAVING sum(bytes) > 10*1024*1800 ORDER BY sum(bytes) DESC LIMIT 20', null, $query->SQL);
		return $this->doSelect($query);
	}

	public function listByIp($ip) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where('l.time > now() - interval \'15 minutes\' GROUP BY l.ip, c.can_admin, c.type_id, c.host, c.id, c.banned HAVING l.ip = \''.$ip.'\' LIMIT 1', null, $query->SQL);
		return $this->doSelect($query);
	}
}
