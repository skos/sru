<?php
/**
 * sprawdza poprawnosc danych
 */
class UFlib_Valid {

	/**
	 * zgodne z wyrazeniem regularnym?
	 * 
	 * @param string $data - tekst
	 * @param string $regexp - wyrazenie regularne
	 * @param string $modifier - modyfikator wyrazenia
	 * @return bool
	 */
	static public function regexp($data, $regexp, $modifier='') {
		return (bool)preg_match('/'.$regexp.'/u'.$modifier, $data);
	}
	
	/**
	 * dlugosc mniejsza równa?
	 * 
	 * @param string $data - tekst
	 * @param int $len - dlugosc
	 * @return bool
	 */
	static public function textMax($data, $len) {
		if (strlen($data) <= $len) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * dlugosc wiêksza równa?
	 * 
	 * @param string $data - tekst
	 * @param int $len - dlugosc
	 * @return bool
	 */
	static public function textMin($data, $len) {
		if (strlen($data) >= $len) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * ciag sklada sie tylko z podanych znakow?
	 * 
	 * @param string $data - ciag
	 * @param string $chars - dozwolone znaki
	 * @param bool $escape - czy escape'owac znaki specjalne?
	 * @return bool
	 */
	static public function onlyChars($data, $chars, $escape=true) {
		if ($escape) {
			$pattern = self::_escapeForPerl($chars);
		} else {
			$pattern = $chars;
		}
		$pattern = '/^['.$pattern.']+$/';
		if (preg_match($pattern, $data) > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * dana jest nullowa?
	 * 
	 * @param mixed $data
	 * @return bool
	 */
	static public function null($data) {
		return '' === (string)$data;
	}

	/**
	* czy ciag jest poprawnym adresem email
	* @param string $txt - ciag do sprawdzenia
	* @return bool - wynik
	*/
	static function email($txt) {
		if (preg_match('/^\w+([-._]\w+)*@\w+([-_]\w+)*\.\w+([-._]\w+)*$/',$txt)) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * czy dana jest wieksza lub rowna liczbie
	 * 
	 * @param mixed $data 
	 * @param int $number - porownywana liczba
	 * @return bool
	 */
	static public function intMin($data, $number) {
		if ((int)$data>=$number) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * czy dana jest mniejsza lub rowna liczbie
	 * 
	 * @param mixed $data 
	 * @param int $number - porownywana liczba
	 * @return bool
	 */
	static public function intMax($data, $number) {
		if ((int)$data<=$number) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * czy dana jest wieksza lub rowna liczbie
	 * 
	 * @param mixed $data 
	 * @param float $number - porownywana liczba
	 * @return bool
	 */
	static public function realMin($data, $number) {
		if ((float)$data>=$number) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * czy dana jest mniejsza lub rowna liczbie
	 * 
	 * @param mixed $data 
	 * @param float $number - porownywana liczba
	 * @return bool
	 */
	static public function realMax($data, $number) {
		if ((float)$data<=$number) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * czy dana zawiera co najmniej okreslona liczbe cyfr
	 * 
	 * @param mixed $data 
	 * @param int $number - ilosc cyfr
	 * @return bool
	 */
	static public function digitsMin($data, $number) {
		$data = preg_replace('/[^\d]+/', '', $data);
		return self::textMin($data, $number);
	}
}
