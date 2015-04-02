<?php
/**
 * mapowanie bazy redis
 */
abstract class UFmapRedis {

	const INT  = 1;
	const TEXT = 2;
	const DATE = 3;
	const TS   = 5;
	const BOOL = 6;
	const REAL = 7;

	const NULL_INT  = 101;
	const NULL_TEXT = 102;
	const NULL_DATE = 103;
	const NULL_TS   = 105;
	const NULL_BOOL = 106;
	const NULL_REAL = 107;


	protected $columns     = array(); // kolumny z bazy danych
	protected $columnTypes = array(); // typy danych, ktore sa w nich skladowane
	protected $valids      = array(); // warunki, ktore musza spelniac modyfikowane dane
	protected $prefix      = '';      // prefix klucza
	protected $pkName      = 'id';    // klucz self::$columns, pod ktorym jest klucz glowny
	protected $pkType      = self::INT; // typ klucza glownego
	protected $autoInc     = 'global:seq:'; // prefix klucza dla pola auto_increment

	protected $_           = ':';     // delimiter w kluczach

	public function __get($var) {
		if (array_key_exists($var, $this->columns)) {
			return $var;
		} else {
			throw UFra::factory('UFex_Core_DataNotFound', $var);
		}
	}

	public function __call($method, $params) {
		if (isset($this->$method)) {
			return $this->$method;
		} else {
			throw UFra::factory('UFex_Core_DataNotFound', $method);
		}
	}

	/**
	 * pobranie nazwy w bazie dla pojedynczej kolumny 
	 * 
	 * @param string $name - nazwa kolumny
	 * @return string - nazwa kolumny w bazie
	 */
	public function column($name=null) {
		if (isset($name)) {
			return $this->columns[$name];
		} else {
			throw UFra::factory('UFex_Core_DataNotFound', $name);
		}
	}

	/**
	 * pobranie typu pojedynczej kolumny
	 * 
	 * @param string $name - nazwa kolumny
	 * @return int - typ
	 */
	public function columnType($name=null) {
		if (isset($this->columnTypes[$name])) {
			return $this->columnTypes[$name];
		} else {
			throw UFra::factory('UFex_Core_DataNotFound', $name);
		}
	}

	/**
	 * pobranie warunkow validacji pojedynczej kolumny
	 * 
	 * @param string $name - nazwa kolumny
	 * @return array - warunki walidacji
	 */
	public function valid($name=null) {
		if (isset($name)) {
			return $this->valids[$name];
		} else {
			throw UFra::factory('UFex_Core_DataNotFound', $name);
		}
	}

	/**
	 * pobranie nazwy kolumny (w bazie) klucza glownego
	 * 
	 * @return string - nazwa kolumny
	 */
	public function prefix() {
		return $this->prefix.$this->_;
	}

	/**
	 * pobranie prefiksu pola auto_increment
	 * 
	 * @return string
	 */
	public function autoInc() {
		return $this->autoInc.$this->prefix;
	}

	/**
	 * pobranie delimitera
	 * 
	 * @return string
	 */
	public function _() {
		return $this->_;
	}
}
