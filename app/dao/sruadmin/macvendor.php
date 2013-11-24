<?php
/**
 * dostawcy MACow
 */
class UFdao_SruAdmin_MacVendor
extends UFdao {

	public function getByMac($mac) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where(
			'substring('.$mapping->column('mac').'::varchar,1,8)=substring(\''.$mac.'\'::varchar,1,8)',
			null, $query->SQL
		);
		$query->order($mapping->mac, $query->DESC);

		return $this->doSelectFirst($query);
	}

}
