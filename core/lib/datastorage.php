<?php
/**
 * skladuje dane
 */
class UFlib_DataStorage {

	/**
	 * dane
	 */
	protected $data = array();

	/**
	 * konstruktor
	 *
	 * @param array - tablica (nazwa => wartosc) z danymi poczatkowymi
	 */
	function __construct(&$initialData = null) {
		if (is_array($initialData)) {
			$this->data =& $initialData;
		}
	}
	
	/**
	 * pobiera dane
	 *
	 * @param string $param - nazwa parametru
	 * @return mixed - wartosc parametru
	 * @throws UFex_Core_DataNotFound - brak parametru
	 */
	function __get($param) {
		if (!isset($this->data[$param])) {
			throw new UFex_Core_DataNotFound('Data "'.$param.'" not found');
		}
		return $this->data[$param];
	}
	
	/**
	 * ustawia jedna dana
	 *
	 * @param string $param - nazwa parametru
	 * @param mixed $value - wartosc parametru
	 */
	function __set($param,$value) {
		$this->data[$param] = $value;
	}
	
	/**
	 * ustawia wiele zmiennych (nadpisuje juz istniejace)
	 *
	 * @param array $params - tablica w postaci nazwa_zmiennej => wartosc
	 */
	function setS($params) {
		if (is_array($params)) {
			$this->data = array_merge($this->data,$params);
		}
	}
	
	/**
	 * pobiera wszystkie dane
	 *
	 * @return array - tablica danych w postaci nazwa_zmiennej => wartosc
	 */
	function getS() {
		return $this->data;
	}
	
	/**
	 * kasuje dana
	 *
	 * @param string $param - nazwa zmiennej
	 */
	function del($param) {
		unset($this->data[$param]);
	}
	
	/**
	 * czysci wszystkie dane
	 */
	function delS() {
		$this->data = array();
	}
	
	/**
	 * sprawdza, czy dana jest ustawiona
	 *
	 * @param string $param - nazwa danej
	 */
	function is($param) {
		return isset($this->data[$param]);
	}
}
