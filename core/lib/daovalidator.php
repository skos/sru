<?php
/**
 * validator dla obiektow DAO
 */
class UFlib_DaoValidator {

	/**
	 * obiekt walidujacy dane
	 */
	protected $validator = null;

	public function __construct() {
		$this->validator =& $this->chooseValidator();
	}
	
	/**
	 * wybiera obiekt walidujaca
	 * 
	 * @return Class
	 */
	protected function &chooseValidator() {
		return UFra::shared('UFlib_Valid');
	}
	
	public function __call($funcName, $params) {
		return call_user_func_array(array($this->validator, $funcName), $params);
	}

	/**
	 * sprawdza typ danej
	 * 
	 * @param int $type - typ
	 * @param mixed $data - dana
	 * @return bool
	 */
	public function type($type, $data) {
		switch ($type) {
			case UFmap::NULL_TS:
			case UFmap::NULL_DATE:
				if ($this->null($data, true)) {
					return true;
				}
			case UFmap::TS:
			case UFmap::DATE:
				if (is_int($data)) {
					return true;
				}
				return (bool)strtotime((string)$data);
			case UFmap::INT:
				return ((string)$data == (string)(int)$data);
			case UFmap::NULL_INT:
				return ($this->validator->null($data) || (string)$data == (string)(int)$data);
			default:
				return true;
		}
	}

	/**
	 * dana jest lub nie jest poprawnym adresem email?
	 * 
	 * @param mixed $data - dana
	 * @param bool $bool - warunek prosty?
	 * @return bool
	 */
	public function email($data, $bool) {
		return !$bool ^ $this->validator->email($data);
	}

	/**
	 * czy dana jest null-em lub wieksza rowna liczbie
	 * 
	 * @param mixed $data 
	 * @param int $number 
	 * @return bool
	 */
	public function intNullMin($data, $number) {
		if ($this->null($data, true)) {
			return true;
		} else {
			return $this->validator->intMin((int)$data, $number);
		}
	}

	/**
	 * czy dana jest null-em lub mniejsza rowna liczbie
	 * 
	 * @param mixed $data 
	 * @param int $number 
	 * @return bool
	 */
	public function intNullMax($data, $number) {
		if ($this->null($data, true)) {
			return true;
		} else {
			return $this->validator->intMax((int)$data, $number);
		}
	}
}
