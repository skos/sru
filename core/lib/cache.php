<?php

/**
 * prosty cache tablicowy
 */
class UFlib_Cache {

	protected $TIME;
	
	protected $data = array();

	public function __construct() {
		$this->TIME = time();
	}
	
	/**
	 * zapisuje wartosc
	 * 
	 * @param string $var - klucz, pod ktorym wartosc bedzie skladowana
	 * @param mixed $val - wartosc
	 * @param int $ttl - czas zycia wartosci (w sekundach)
	 */
	public function set($var, $val, $ttl=10) {
		$this->data[$var] = array (
			'time'  => $this->TIME + $ttl,
			'value' => $val,
		);
	}

	/**
	 * pobiera wartosc
	 * 
	 * @param string $var - klucz
	 * @return mixed - wartosc spod danego klucza
	 */
	public function get($var) {
		if (!$this->is($var)) {
			throw UFra::factory('UFex_Core_DataNotFound', 'Data key "'.$var.'" not found');
		}
		return $this->data[$var]['value'];
	}

	/**
	 * kasuje wartosc
	 * 
	 * @param string $var - klucz
	 */
	public function del($var) {
		unset($this->data[$var]);
	}

	/**
	 * sprawdza czy jest cache o danym kluczu
	 * 
	 * @param string $var - klucz
	 * @return bool - wynik sprawdzenia
	 */
	public function is($var) {
		if (isset($this->data[$var])) {
			if (time() < $this->data[$var]['time']) {
				return true;
			} else {
				$this->del($var);
				return false;
			}
		} else {
			return false;
		}
	}
}
