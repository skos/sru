<?php
/**
 * dorm
 */
class UFdao_SruAdmin_Dorm
extends UFdao {
	public function listAll() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		
		$query->order($mapping->id, $query->ASC);
	
		return $this->doSelect($query);
	}
}
