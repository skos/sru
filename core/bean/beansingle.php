<?php
/**
 * reprezentacja danych
 */
abstract class UFbeanSingle
extends UFbean {
	
	/**
	 * zrodlo aktualnych danych
	 */
	protected $source = null;

	/**
	 * dane, ktore ulegly zmianie
	 */
	protected $dataChanged = array();

	/**
	 * lapie wszystkie niezdefiniowane metody
	 *
	 * automatycznie uruchamia z dao te, ktore rozpoczynaja sie na "get"
	 */
	public function __call($method, $params) {
		$classMethod = array($this->dao, $method);
		if (is_callable($classMethod)) {
			if (strpos($method, 'get') === 0) {
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
	 * przykladowa metoda walidujaca pole "XyzSample"
	 * 
	 * @param mixed $val - wartosc
	 * @param bool $change - zmiana wartosci danej?
	 * @return string/null - nazwa warunku/ow, ktore nie zostaly spelnione
	 */
	protected function validateXyzSample($val, $change) {
	}

	/**
	 * przykladowa metoda normalizujaca pole "XyzSample"
	 * 
	 * @param mixed $val - wartosc
	 * @param bool $change - zmiana wartosci danej?
	 * @return mixed - znormalizowana dana
	 */
	protected function normalizeXyzSample($val, $change) {
	}

	/**
	 * sprawdza poprawnosc dowolnego pola na podstawie DAO
	 * 
	 * @param string $var - nazwa pola
	 * @param mixed $val - wartosc
	 * @param bool $change - czy nastepuje zmiana?
	 */
	public function validate($var, $val, $change) {
		$this->dao->validate($var, $val, $change);
	}

	/**
	 * przypisuje nowa wrtosc do parametru
	 * 
	 * @param string $var - nazwa parametru
	 * @param mixed $val - wartosc parametru
	 */
	public function __set($var, $val) {
		$change = array_key_exists($var, $this->data);
		$this->validate($var, $val, $change);
		if (method_exists($this, 'validate'.ucfirst($var))) {
			$error = call_user_func(array(&$this, 'validate'.ucfirst($var)), $val, $change);
			if (is_string($error)) {
				$error = array($var => $error);
			}
			if (is_array($error)) {
				throw UFra::factory('UFex_Dao_DataNotValid', 'Data "'.$var.'" is not valid', 0, E_WARNING, $error);
			}
		}
		if (method_exists($this, 'normalize'.ucfirst($var))) {
			$valNorm = call_user_func(array(&$this, 'normalize'.ucfirst($var)), $val, $change);
		} else {
			$valNorm = $this->dao->normalize($var, $val, $change);
		}
		if (!array_key_exists($var, $this->data) || $valNorm !== $this->data[$var]) {
			$this->dataChanged[$var] = $valNorm;
		}
		$this->data[$var] = $valNorm;
	}

	/**
	 * pobirea wartosc parametru
	 * 
	 * @param string $var - nazwa parametru
	 * @return mixed - wartosc
	 * @throws Ex_Bean_ParamNotFound - nieznany parametr
	 */
	public function __get($var) {
		if (array_key_exists($var, $this->data)) {
			return $this->data[$var];
		} else {
			throw new UFex_Core_DataNotFound('Data "'.$var.'" not found');
		}
	}

	/**
	 * pobiera dane po identyfikatorze
	 * 
	 * @param int $id - identyfikator
	 */
	public function getByPK($id) {
		$this->data = $this->dao->getByPK($id);
		$this->source = self::SOURCE_DAO;
	}

	/**
	 * skasuj
	 */
	public function del() {
		return $this->dao->del($this->data);
	}

	/**
	 * zmienia dane
	 * 
	 * @return int - ile rekordow zostalo zmienionych (praktycznie zawsze 1)
	 */
	protected function edit() {
		return $this->dao->edit($this->dataChanged, $this->data);
	}

	/**
	 * dodaje dana
	 * 
	 * @param bool $lastVal - czy pobierac wartosc autoincrement?
	 * @return int - identyfikator nowododanego rekordu
	 */
	protected function add($lastVal = true) {
		return $this->dao->add($this->data, $lastVal);
	}

	/**
	 * zapisz aktualne dane
	 * 
	 * @param bool $lastVal - czy pobierac wartosc autoincrement, gdy dodawana jest nowa wartosc?
	 * @return int - idengyfikator nowododanego rekordu lub ilosc
	 *               zmodyfikowanych rekordow w zaleznosci od tego, skad
	 *               pochodzily dane
	 */
	public function save($lastVal = true) {
		if (self::SOURCE_DAO == $this->source) {
			return $this->edit();
		} else {
			return $this->add($lastVal);
		}
	}

	/**
	 * wypelnienie struktury danymi z tablicy
	 * 
	 * @param array $data - dane
	 */
	public function fill(array &$data) {
		$errors = array();
		foreach ($data as $var=>$val) {
			try {
				$this->__set($var, $val);
			} catch (UFex_Dao_DataNotValid $e) {
				$errors = $errors + $e->getData();
			} catch (UFex_Core_DataNotFound $e) {
			}
		}
		if (count($errors)) {
			throw UFra::factory('UFex_Dao_DataNotValid', 'Some data is not valid', 0, E_WARNING, $errors);
		}
	}
	
	/**
	 * wypelnia danymi z POST-a
	 *
	 * @param string $key - nazwa zmiennej w post z danymi z formularza do wypelnienia
	 * @param array $filterOut - ktore elementy post-a zignorowac
	 * @param array $filter - jedynie te elementy post-a maja byc brane pod uwage
	 * @return bool - czy byly dane w POST?
	 */
	public function fillFromPost($key, $filterOut=null, $filter=null) {
		try {
			$post = $this->_srv->get('req')->post->{$key};
			if (is_array($filterOut)) {
				foreach ($filterOut as $field) {
					unset($post[$field]);
				}
			}
			if (is_array($filter)) {
				foreach ($post as $field=>$tmp) {
					if (!in_array($field, $filter)) {
						unset($post[$field]);
					}
				}
			}
			foreach ($post as &$data) {
				$data = stripslashes($data);
			}
			$this->fill($post);
			return true;
		} catch (UFex_Core_DataNotFound $e) {
			return false;
		}
	}
}
