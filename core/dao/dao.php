<?php
/**
 * klasa dostepu do danych z relacyjnej bazy danych
 */
abstract class UFdao 
extends UFdaoBase {

	/**
	 * wstepne przygotowanie parametorw do dodania danych
	 * 
	 * @param string $mapping - zktorego mapowania skorzystac
	 * @param array $data - dane
	 * @return Lib_Db_QueryInsert - parametry zapytania
	 */
	protected function prepareInsert(UFmap $mapping, array $data) {
		$query = UFra::factory('UFlib_Db_Query');

		$query->tables($mapping->tables());
		$query->values($mapping->columns(),
		               $data,
		               $mapping->columnTypes());
		return $query;
	}

	/**
	 * wstepne przygotowanie parametrow do pobrania danych
	 * 
	 * @param string $map - z ktorego mapowania skorzystac
	 * @return Lib_Db_QuerySelect - parametry zapytania
	 */
	protected function prepareSelect(UFmap $mapping) {
		$query = UFra::factory('UFlib_Db_Query');

		$query->tables($mapping->tables());
		$query->joins($mapping->joins(), $mapping->joinOns());
		$query->columns($mapping->columns(),$mapping->columnTypes());
		foreach ($mapping->wheres() as $where) {
			$query->where($where, null, $query->SQL);
		}
		return $query;
	}

	/**
	 * przygotowanie parametrow do usuniecia danych
	 * 
	 * @param string $map - z ktorego mapowania skorzystac
	 * @return Lib_Db_QueryDelete - parametry zapytania
	 */
	protected function prepareDelete(UFmap $mapping) {
		$query = UFra::factory('UFlib_Db_Query');

		$query->tables($mapping->tables());
		$query->columns($mapping->columns(),$mapping->columnTypes());
		foreach ($mapping->wheres() as $where) {
			$query->where($where, null, $query->SQL);
		}
		$query->pk($mapping->pk(), $mapping->pkType());

		return $query;
	}

	/**
	 * przygotowanie parametrow do aktualizacji danych
	 * 
	 * @param string $map - z ktorego mapowania skorzystac
	 * @return Lib_Db_QueryUpdate - parametry zapytania
	 */
	protected function prepareUpdate(UFmap $mapping, array $data) {
		$query = UFra::factory('UFlib_Db_Query');

		$query->tables($mapping->tables());
		$query->joins($mapping->joins(), $mapping->joinOns());
		$query->values($mapping->columns(), $data, $mapping->columnTypes());
		foreach ($mapping->wheres() as $where) {
			$query->where($where, null, $query->SQL);
		}
		$query->pk($mapping->pk(), $mapping->pkType());

		return $query;
	}

	/**
	 * pobranie dancyh
	 * 
	 * @param UFlib_Db_Query $query - parametry zapytania
	 * @return array - dane
	 */
	protected function doSelect(UFlib_Db_Query $query) {
		$tmp = $this->db->select($query);
		if (isset($tmp[0])) {
			return $tmp;
		} else {
			throw UFra::factory('UFex_Dao_NotFound', 'Data not found');
		}
	}

	/**
	 * pierwszy wiersz pobranych danych
	 * 
	 * @param UFlib_Db_Query $query - parametry zapytania
	 * @return array - dane
	 */
	protected function doSelectFirst(UFlib_Db_Query $query) {
		$query->limit(1);
		$tmp = $this->doSelect($query);
		if (isset($tmp[0])) {
			return $tmp[0];
		} else {
			throw UFra::factory('UFex_Dao_NotFound', 'Data not found');
		}
	}

	/**
	 * aktualizacja danych
	 * 
	 * @param UFlib_Db_QueryUpdate $query - parametry zapytania
	 * @return int - ilosc zmodyfikowanych danych
	 */
	protected function doUpdate(UFlib_Db_Query $query) {
		return $this->db->update($query);
	}

	/**
	 * skasowanie danych
	 * 
	 * @param UFlib_Db_QueryDelete $query - parametry zapytania
	 * @return int - liczba usunietych danych
	 */
	protected function doDelete(UFlib_Db_Query $query) {
		return $this->db->delete($query);
	}

	/**
	 * wstawienie danych
	 * 
	 * @param UFlib_Db_QueryInsert $query - parametry zapytania
	 * @param bool $lastVal - czy pobierac wartosc autoincrement?
	 * @return int/null - wartosc auto_increment dodanego rekordu
	 */
	protected function doInsert(UFlib_Db_Query $query, $lastVal = true) {
		return $this->db->insert($query, $lastVal);
	}

	/**
	 * pobranie danych po identyfikatorze
	 * 
	 * @param int $pk - identyfikator
	 * @return array - dane
	 */
	public function getByPK($pk) {
		$key = $this->cachePrefix.'/'.__FUNCTION__.'/'.$pk;
		try {
			return $this->cacheGet($key);
		} catch (UFex_Core_DataNotFound $e) {
			$mapping = $this->mapping('get');

			$query = $this->prepareSelect($mapping);
			$query->where($mapping->pkName(), $pk);

			$return = $this->doSelectFirst($query);
			$this->cacheSet($key, $return);
			return $return;
		}
	}

	/**
	 * pobranie listy danych wg identyfikatorow
	 * 
	 * @param array $pks - lista identyfikatorow
	 * @return array - dane
	 */
	public function listByPK(array $pks) {
		if (empty($pks)) {
			throw UFra::factory('UFex_Core_NoParam', 'pks');
		}
		$key = $this->cachePrefix.'/'.__FUNCTION__.'/'.implode(',', $pks);
		try {
			return $this->cacheGet($key);
		} catch (UFex_Core_DataNotFound $e) {
			$mapping = $this->mapping('list');

			$query = $this->prepareSelect($mapping);
			$query->where($mapping->pkName(), $pks, $query->IN);

			$return = $this->doSelect($query);
			$this->cacheSet($key, $return);
			return $return;
		}
	}

	/**
	 * pobranie listy ostatnich danych
	 * 
	 * @param int $page - numer strony z danymi
	 * @param int $perPage - ilosc rekordow na stronie
	 * @param int $perPageOffset - o ile rekordow za duzo jest podane w $perPage
	 * @return array - dane
	 */
	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$key = $this->cachePrefix.'/'.__FUNCTION__.'/'.$page.'/'.$perPage.'/'.$overFetch;
		try {
			return $this->cacheGet($key);
		} catch (UFex_Core_DataNotFound $e) {
			$mapping = $this->mapping('list');

			$query = $this->prepareSelect($mapping);
			$query->limit($perPage+$overFetch);
			$query->order($mapping->pkName(), $query->DESC);
			$query->offset($this->findOffset($page, $perPage));

			$return = $this->doSelect($query);
			$this->cacheSet($key, $return);
			return $return;
		}
	}

	/**
	 * zapisanie danych
	 * 
	 * @param array $data - dane do zapisania
	 * @param array $dataAll - wszystkie dane
	 * @return int - ilosc zmodyfikowanych rekordow
	 */
	public function edit(array $data, array $dataAll=array()) {
		$mapping = $this->mapping('set');

		$query = $this->prepareUpdate($mapping, $data);
		if (count($query->values) == 0) {
			UFra::debug('Nothing to change');
			return 0;
		}
		$query->where($mapping->pkName(), $dataAll[$mapping->pkName()]);

		$return = $this->doUpdate($query);
		$this->cacheDel($this->cachePrefix.'/getByPK/'.$dataAll[$mapping->pkName()]);
		return $return;
	}

	/**
	 * usuniecie danych
	 * 
	 * @param array $data - dane do usuniecia
	 * @return int - ilosc usunietych rekordow
	 */
	public function del(array $data) {
		$mapping = $this->mapping('del');
		
		$query = $this->prepareDelete($mapping, $data);
		$query->where($mapping->pkName(), $data[$mapping->pkName()]);

		$return = $this->doDelete($query);
		$this->cacheDel($this->cachePrefix.'/getByPK/'.$data[$mapping->pkName()]);
		return $return;
	}

	/**
	 * dodanie rekordu
	 * 
	 * @param array $data - dane
	 * @param bool $lastVal - czy pobierac wartosc autoincrement?
	 * @return int - identyfikator nowododanego rekordu
	 */
	public function add(array $data, $lastVal = true) {
		$mapping = $this->mapping('add');

		$query = $this->prepareInsert($mapping, $data);

		return $this->doInsert($query, $lastVal);
	}
}
