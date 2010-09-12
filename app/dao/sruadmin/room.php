<?php
/**
 * akademik
 */
class UFdao_SruAdmin_Room
extends UFdao {

	public function listByDormitoryId($dormitoryId) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->dormitoryId, $dormitoryId);
		$query->order($mapping->id);

		return $this->doSelect($query);
	}
	public function getByAlias($dormitoryAlias, $alias) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		
		$query->where($mapping->alias, $alias);
		$query->where($mapping->dormitoryAlias, $dormitoryAlias);

		return $this->doSelectFirst($query);
	}

	public function listAllOrdered() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->id);

		return $this->doSelect($query);
	}
}
