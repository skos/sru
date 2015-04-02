<?
/**
 * historia urzadzenia
 */
class UFdao_SruAdmin_DeviceHistory
extends UFdao {

	public function listByDeviceId($id, $count=null) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->deviceId, $id);
		$query->order($mapping->pkName(), $query->DESC);
		if (is_int($count)) {
			$query->limit($count);
		}

		return $this->doSelect($query);
	}
}
