<?php
/**
 * reprezentacja listy danych
 */
abstract class UFbeanList
extends UFbean
implements Iterator, Countable, ArrayAccess {

	/**
	 * lapie wszystkie niezdefiniowane metody
	 *
	 * automatycznie uruchamia z dao te, ktore rozpoczynaja sie na "list"
	 */
	public function __call($method, $params) {
		$classMethod = array($this->dao, $method);
		if (is_callable($classMethod)) {
			if (strpos($method, 'list') === 0) {
				$this->data = call_user_func_array($classMethod, $params);
				$this->source = self::SOURCE_DAO;
				UFra::debug('Auto-call DAO function '.$method.' '.print_r($params, true));
			} else {
				UFra::debug('Method '.$method.' not called');
			}
		} else {
			throw UFra::factory('UFex_Core_NoMethod', $method);
		}
	}
	
	/**
	 * akutalny element interatora
	 * 
	 * @return mixed
	 */
	public function current() {
		return current($this->data);
	}

	/**
	 * klucz aktualnego elementu iteratora
	 * 
	 * @return mixed
	 */
	public function key() {
		return key($this->data);
	}

	/**
	 * przejscie do nastepnego elementu iteratora
	 */
	public function next() {
		next($this->data);
	}

	/**
	 * wyzerowanie pozycji iteratora
	 */
	public function rewind() {
		reset($this->data);
	}

	/**
	 * sprawdzenie, czy element istnieje
	 * @return bool
	 */
	public function valid() {
		return $this->current() !== false;
	}

	/**
	 * zlicza ilosc elementow w danych
	 * 
	 * @return int
	 */
	public function count() {
		return count($this->data);
	}

	public function offsetExists($offset) {
		return isset($this->data[$offset]);
	}

	public function offsetGet($offset) {
		return $this->data[$offset];
	}

	public function offsetSet($offset, $value) {
		throw UFra::factory('UFex_Core_NoMethod', 'Read only value "'.$offset.'"');
	}

	public function offsetUnset($offset) {
		unset($this->data[$offset]);
	}
}
