<?php

/**
 * przechowuje dane dotyczace requestu
 */
class UFlib_Request
extends UFlib_DataStorage {

	/**
	 * sparsowane segmenty url-a
	 */
	protected $segments = array();

	/**
	 * przesuniecie w numeracji segmentow
	 */
	protected $offset = 0;
	
	public function __construct() {
		$req['post']   = UFra::factory('UFlib_Datastorage', $_POST);
		$req['get']    = UFra::factory('UFlib_Datastorage');
		$req['files']  = UFra::factory('UFlib_Datastorage', $_FILES);
		$req['server'] = UFra::factory('UFlib_Datastorage', $_SERVER);
		$req['cookie'] = UFra::factory('UFlib_Datastorage', $_COOKIE);
		$this->data =& $req;
		$segments = rtrim($req['server']->REQUEST_URI, '/');
		$segments = substr($segments, strlen(UFURL_BASE));
		$this->segments = explode('/', $segments);
		unset($this->segments[0]);
	}

	/**
	 * zwraca tresc konkretnego czlonu request_uri
	 * 
	 * @param int $no - numer czlonu
	 * @return string
	 */
	public function segment($no) {
		if (!is_int($no)) {
			throw UFra::factory('UFex_Core_NoParameter', 'number');
		}
		$no += $this->offset;
		if (!isset($this->segments[$no])) {
			throw UFra::factory('UFex_Core_DataNotFound', 'segment '.$no);
		}
		return $this->segments[$no];
	}

	/**
	 * poczatkowe segmentu adresu
	 * 
	 * @param int $number - koncowy segment, liczony od offsetu
	 * @return array
	 */
	public function segments($number=null) {
		if (is_int($number)) {
			$number += $this->offset;
			if ($number < 1) {
				return array();
			} else {
				return array_slice($this->segments, 0, $number);
			}
		} else {
			return $this->segments;
		}
	}

	/**
	 * ilosc segmentow
	 * 
	 * @return int
	 */
	public function segmentsCount() {
		return count($this->segments)-$this->offset;
	}

	/**
	 * usuwa wartosci segmentow od podanej pozycji
	 * 
	 * @param int $no - numer czlonu
	 */
	public function segmentCutFrom($no) {
		if (!is_int($no)) {
			throw UFra::factory('UFex_Core_NoParameter', 'number');
		}
		$no += $this->offset;
		$count = $this->segmentsCount();
		for ($no; $no<=$count; $no++) {
			unset ($this->segments[$no]);
		}
	}

	/**
	 * przesuwa sie "do przodu" w numeracji segmentow
	 * 
	 * @param int $count - o ile przesunac
	 * @return bool - czy udalo sie przesunac
	 */
	public function forward($count=1) {
		if (!is_int($count)) {
			throw UFra::factory('UFex_Core_NoParameter', 'count');
		}
		if ($this->segmentsCount() >= $count) {
			$this->offset += $count;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * przesuwa sie "do tylu" w numeracji segmentow
	 * 
	 * @param int $count - o ile przesunac
	 * @return bool - czy udalo sie przesunac
	 */
	public function backward($count=1) {
		if (!is_int($count)) {
			throw UFra::factory('UFex_Core_NoParameter', 'count');
		}
		if ($this->offset >= $count) {
			$this->offset -= $count;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * czysci offset
	 */
	public function rewind() {
		$this->offset = 0;
	}

	public function __set($param, $value) {
		throw UFra::factory('UFex_Core_NoMethod', '__set');
	}

	public function setS($params) {
		throw UFra::factory('UFex_Core_NoMethod', '__set');
	}

	public function del($params) {
		throw UFra::factory('UFex_Core_NoMethod', '__set');
	}

	public function delS() {
		throw UFra::factory('UFex_Core_NoMethod', '__set');
	}
	
	public static function getCookie($name){
		if(array_key_exists($name, $_COOKIE)){
			return $_COOKIE[$name];
		}
		
		return false;
	}
} 
