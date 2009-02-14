<?
/**
 * wspolny kontroler
 */
class UFctl_Common
extends UFctl {
	
	/**
	 * zwraca poszukiwany parametr z danego segmentu
	 *
	 * @param int $no - nr segmentu
	 * @param string $param - nazwa parametru
	 * @return string/null - nr strony; null - nie znaleziono parametru lub byl pusty
	 */
	protected function fetchParam($no, $param) {
		$tmp = explode(':', $this->_srv->get('req')->segment($no), 2);
		if (isset($tmp[1]) && $param === $tmp[0]) {
			return $tmp[1];
		} else {
			return null;
		}
	}
	
	/**
	 * parsuje tekst, aby wyciagnac nr strony
	 *
	 * zwraca wartosc 1 (jeden), gdy nie udalo sie znalezc nr strony
	 * 
	 * @param int $no - nr segmentu
	 * @param string $param - nazwa parametru przechowujacego nr strony
	 * @return int - nr strony
	 */
	protected function fetchPage($no, $param='strona') {
		try {
			$page = (int)$this->fetchParam($no, $param);
			if ($page < 1) {
				$page = 1;
			}
		} catch (Exception $e) {
			$page = 1;
		}
		return $page;
	}
	
	/**
	 * sprawdza, czy segment zawiera poszukiwany parametr
	 *
	 * @param int $no - nr segmentu
	 * @param string $regexp - regexp, do ktorego ma pasowac parametr
	 * @return bool - czy zawiera parametr
	 */
	protected function isParam($no, $regexp) {
		try {
			if (UFlib_Valid::regexp($this->_srv->get('req')->segment($no), '^'.$regexp.'$')) {
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * sprawdza, czy segment zawiera parametr typu int
	 *
	 * @param int $no - nr segmentu
	 * @param string $regexp - nazwa parametru
	 * @return bool - czy zawiera parametr
	 */
	function isParamInt($no, $param) {
		return $this->isParam($no, $param.':[0-9]+');
	}

	/**
	 * sprawdza, czy segment zawiera informacje o stronicowaniu
	 *
	 * @param int $no - nr segmentu
	 * @param string $param - nazwa parametru przechowujacego nr strony
	 * @return bool - czy zawiera stronicowanie?
	 */
	protected function isPage($no, $param='strona') {
		return $this->isParamInt($no, $param);
	}
}
