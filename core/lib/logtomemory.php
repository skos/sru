<?php
/**
* zbiera logi w pamieci
*/
class UFlib_LogToMemory
extends UFlib_Log {

	/**
	 * dane
	 */
	protected $logs;

	/**
	 * czas rozpoczecia logowania
	 */
	protected $start;

	/**
	* konstruktor
	*/
	public function __construct() {
		$this->logs = array();
		$this->start = microtime(true);
	}
	
	/**
	* dopisuje loga
	* @param string $desc - opis
	* @param int $level - poziom bledu
	* @param mixed $data - dane
	*/
	protected function add($desc, $level, $data = null) {
		/*
		$debug = debug_backtrace();

		if (isset($debug[2])) {
			$debug1 = $debug[2];
		} else {
			$debug1 = $debug[1];
		}
		if ($debug1['function'] == '__call') {
			$debug1 = $debug[3];
		}
		if (!isset($debug1['file'])) {
			$debug1['line'] = $debug1['file'] = '???';
		}
		$log['file'] = $debug1['file'];
		$log['line'] = (int)$debug1['line'];
		$log['func'] = $debug1['function'];
		unset($debug1['object'], $debug1['args']);
		if (!empty($debug1['type'])) {
			$log['class'] = $debug1['class'];
		} else {
			$log['class'] = '';
		}
		*/
		$log['desc'] = $desc;
		$log['data'] = $data;
		$log['level'] = $level;
		$log['time']  = microtime(true);
		$this->logs[] = $log;
	}

	public function __toString() {
		$txt = '';
		foreach ($this->logs as $data) {
			$txt .= str_pad(round(($data['time']-$this->start)*1000, 1),6,' ',STR_PAD_LEFT).': '.$data['desc'];
			if (is_array($data['data']) || is_object($data['data'])) {
				$txt .= ': '.htmlspecialchars(print_r($data['data'], true));
			} else {
				$txt .= ': '.htmlspecialchars($data['data']);
			}
			$txt .= CR;
		}
		return $txt;
	}
}
