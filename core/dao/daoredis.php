<?php
/**
 * klasa dostepu do danych z redisa
 */
abstract class UFdaoRedis
extends UFdaoBase {

	/**
	 * przygotowuje tablice danych do zapisu do bazy
	 * 
	 * @param UFmapRedis $mapping - uzyty mapping
	 * @param array $data - dane
	 * @return string
	 */
	protected function encode(UFmapRedis $mapping, $data) {
		$return = array();
		$cols = $mapping->columns();
		foreach ($data as $key=>&$val) {
			$return[$cols[$key]] =& $val;
		}
		unset($data);
		return json_encode($return);
	}

	/**
	 * konwertuje zapis bazodanowy na czytelna tablice z danymi
	 * 
	 * @param UFmapRedis $mapping - uzyty mapping
	 * @param string/array $data - dane z bazy
	 * @return array
	 */
	protected function decode(UFmapRedis $mapping, $data) {
		$return = array();
		$cols = array_flip($mapping->columns());
		if (!is_array($data)) {
			$data = json_decode($data, true);
			foreach ($data as $key=>&$val) {
				$return[$cols[$key]] =& $val;
			}
			unset($data);
			return $return;
		} else {	// wielowierszowy wynik
			foreach ($data as $d) {
				$d = json_decode($d, true);
				$row = array();
				foreach ($d as $key=>&$val) {
					$row[$cols[$key]] =& $val;
				}
				unset($d);
				$return[] = $row;
			}
			return $return;
		}
	}

	/**
	 * pobranie danych po identyfikatorze i je deserializuje
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

			$return = $this->db->get($mapping->prefix().$mapping->_().$pk);
			if (null === $return) {
				throw UFra::factory('UFex_Dao_NotFound', 'Data not found');
			}
			$return = $this->decode($mapping, $return);

			$this->cacheSet($key, $return);
			return $return;
		}
	}

	protected function prepend($prefix, array $names) {
		foreach ($names as $k=>&$name) {
			$name = $prefix.$name;
		}
		return $names;
	}

	public function listByPKs(array $pks) {
		$mapping = $this->mapping('list');
		$pks = $this->prepend($mapping->prefix().$mapping->_(), $pks);
		$data = $this->db->mget($pks);
		if (null === $data) {
			throw UFra::factory('UFex_Dao_NotFound', 'Data not found');
		}
		return $this->decode($mapping, $data);
	}

	/**
	 * zapisanie danych
	 * 
	 * @param array $data - dane do zapisania
	 * @param array $dataAll - wszystkie dane
	 * @return int - ilosc zmodyfikowanych rekordow
	 */
	public function edit(array $data, array $dataAll=array()) {
		if (count($data) == 0) {
			UFra::debug('Nothing to change');
			return 0;
		}
		$mapping = $this->mapping('set');

		$id = $dataAll[$mapping->pkName()];

		$dataAll = $data + $this->getByPK($id);
		$dataAll = $this->encode($mapping, array_intersect_key($dataAll, $mapping->columns()));

		$return = $this->db->set($mapping->prefix().$mapping->_().$id, $dataAll);
		$this->cacheDel($this->cachePrefix.'/getByPK/'.$id);
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
		
		$id = $data[$mapping->pkName()];

		$return = $this->db->delete($mapping->prefix().$mapping->_().$id);

		$this->cacheDel($this->cachePrefix.'/getByPK/'.$id);
		return $return;
	}

	/**
	 * dodanie rekordu
	 * 
	 * @param array $data - dane
	 * @return int - identyfikator nowododanego rekordu
	 */
	public function add(array $data) {
		$mapping = $this->mapping('add');

		$id = $this->db->incr($mapping->autoInc());

		$data = array_intersect_key($data, $mapping->columns());
		$data[$mapping->pkName()] = $id;
		$data = $this->encode($mapping, $data);

		$this->db->set($mapping->prefix().$mapping->_().$id, $data);
		return $id;
	}
}
