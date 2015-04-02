<?php
/**
 * klasa bazowa klas loggera
 */
abstract class UFlib_Log {

	/**
	 * poziom, od ktorego jest logowane
	 */
	protected $level = E_ERROR;

	/**
	* dopisuje loga
	* @param string $desc - opis
	* @param int $level - poziom bledu
	* @param mixed $data - dane
	*/
	abstract protected function add($desc, $level, $data = null);

	/**
	 * ustawia poziom logowania
	 * 
	 * @param int $level - poziom logowania
	 */
	public function setLevel($level = E_ERROR) {
		$this->level = (int)$level;
	}

	/**
	 * dodaje wiadomosc debugowa
	 * 
	 * @param string $desc - opis
	 * @param mixed $data - dane
	 */
	public function debug($desc, $data=null) {
		if ($this->level >= E_NOTICE) {
			$this->add($desc, E_NOTICE, $data);
		}
	}

	/**
	 * dodaje wiadomosc informacyjna
	 * 
	 * @param string $desc - opis
	 * @param mixed $data - dane
	 */
	public function info($desc, $data=null) {
		if ($this->level >= E_WARNING) {
			$this->add($desc, E_WARNING, $data);
		}
	}

	/**
	 * doddaje wiadomosc o bledzie
	 * 
	 * @param string $desc - opis
	 * @param mixed $data - dane
	 */
	public function error($desc, $data=null) {
		if ($this->level >= E_ERROR) {
			$this->add($desc, E_ERROR, $data);
		}
	}

	public function &wrap(&$class) {
		if (!is_object($class)) {
			throw new Ex_Core_NoParameter('Parameter "class" not found.');
		}
		return new Lib_LogWrapper($class, $this);
	}
}
