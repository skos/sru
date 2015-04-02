<?php
/**
 * model switcha
 */
class UFdao_SruAdmin_SwitchModel
extends UFdao {

	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->model);

		return $this->doSelect($query);
	}
}
