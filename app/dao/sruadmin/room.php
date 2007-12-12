<?php
/**
 * akademik
 */
class UFdao_SruAdmin_Room
extends UFdao {

	public function listAll($dormitoryAlias) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->dormitoryAlias, $dormitoryAlias);
		$query->order($mapping->alias);

		return $this->doSelect($query);
	}
	public function getByAlias($dormitoryAlias, $alias) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		
		$query->where($mapping->alias, $alias);
		$query->where($mapping->dormitoryAlias, $dormitoryAlias);

		return $this->doSelectFirst($query);
	}	
}
