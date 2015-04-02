<?php
/**
 * akademik
 */
class UFdao_SruAdmin_Room
extends UFdao {

	public function listByDormitoryId($dormitoryId, $waletOnly = false) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->dormitoryId, $dormitoryId);
		if ($waletOnly) {
			$query->where($mapping->typeId, UFbean_SruAdmin_Room::TYPE_WALET_MAX, UFlib_Db_Query::LTE);
		}
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
		$query->where($mapping->typeId, UFbean_SruAdmin_Room::TYPE_STUDENT);
		$query->order($mapping->id);

		return $this->doSelect($query);
	}
}
