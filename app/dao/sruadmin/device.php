<?php
/**
 * urządzenie
 */
class UFdao_SruAdmin_Device
extends UFdao {
	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->deviceType);

		return $this->doSelect($query);
	}
}