<?
/**
 * transfer
 */
class UFdao_SruAdmin_Transfer
extends UFdao {

	public function listTop() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where('l.time > now() - interval \'30 minutes\' GROUP BY mac HAVING sum(bytes) > 10*1024*1800 ORDER BY sum(bytes) DESC LIMIT 100', null, $query->SQL);
		//$query->where('l.time > now() - interval \'30 minutes\'', null, $query->SQL);
		//$query->groupBy('mac');
		return $this->doSelect($query);
	}
}
