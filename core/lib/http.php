<?php
/**
* obsluga protokolu http
*/
class UFlib_Http {

	const OK = '200';
	const SEE_OTHER = '303';
	const MOVED_PERMANENTLY = '301';
	const NOT_MODIFIED = '304';
	const BAD_REQUEST = '400';
	const NOT_AUTHORISED = '401';
	const FORBIDDEN = '403';
	const NOT_FOUND = '404';

	const AUTHENTICATION_BASIC = 'Basic';

	/**
	 * parsuje naglowek wyslany przez przegladarke i zwraca preferencje
	 * uzytkownika/przegladarki (w kolejnosci od najbardziej pozadanej)
	 * 
	 * @param string $txt - naglowek http wyslany przez przegladarke
	 * @return array(string/true) - uporzadkowana lista wartosci (true oznacza dowolne)
	 */
	public static function negotiate($txt) {
		$parts = explode(',', $txt);
		if (count($parts)==0) {
			return array(true);
		}

		$prefs = array();
		foreach ($parts as $part) {
			preg_match('/^(.*)(; *q=(\d+(\.\d+)?))?$/U', $part, $tmp);
			$var = trim(trim($tmp[1]));
			if ('*' === $var) {
				$var = true;
			}
			if (isset($tmp[3])) {
				$val = (float)$tmp[3];
			} else {
				$val = 1.0;
			}
			if ($val > 0) {
				$prefs[$val][] = $var;
			}
		}
		$out = array();
		krsort($prefs);
		foreach ($prefs as $pref) {
			foreach ($pref as $p) {
				$out[] = $p;
			}
		}
		return $out;
	}

	/**
	 * przekierowanie
	 *
	 * jezeli $url nie rozpoczyna sie od "http", to zostanie uzyty aktualny adres serwera
	 * 
	 * @param string $url - url, pod ktory ma nastapic przekierowanie
	 * @param string $code - kod statusu http przekierowania
	 */
	static public function redirect($url, $code=self::SEE_OTHER) {
		if (0 !== strpos($url, 'http')) {
			$server = UFra::services()->get('req')->server;
			if ($server->is('HTTPS')) {
				$url = 'https://'.$server->HTTP_HOST.$url;
			} else {
				$url = 'http://'.$server->HTTP_HOST.$url;
			}
		}
		switch ($code) {
			case self::SEE_OTHER:
				self::seeOther($url);
				break;
			default:
				header('HTTP/1.1 '.$code);
				header('Location: '.$url);
				break;
		}
		echo '<html><head><meta http-equiv="Refresh" content="0; url='.$url.'"></head><body><p><a href="'.urlencode($url).'">Redirect</a></body></p></html>';
		exit;
	}

	/**
	 * przejscie pod inny adres
	 * 
	 * @param string $url - nowy url
	 */
	static public function seeOther($url) {
		header('HTTP/1.1 '.self::SEE_OTHER);
		header('Location: '.$url);
	}

	/**
	 * wyslanie statusu o braku autoryzacji
	 * 
	 * @param string $realm - opis identyfikujacy zasob
	 * @param string $type - typ autoryzacji
	 */
	static public function notAuthorised($realm='', $type=self::AUTHENTICATION_BASIC) {
		header('WWW-Authenticate: '.$type.' realm="'.$realm.'"');
		header('HTTP/1.1 '.self::NOT_AUTHORISED);
	}

	/**
	 * wyslanie statusu o braku uprawnien
	 */
	static public function forbidden() {
		header('HTTP/1.1 '.self::FORBIDDEN);
	}

	/**
	 * wyslanie statusu o braku strony
	 */
	static public function notFound() {
		header('HTTP/1.1 '.self::NOT_FOUND);
	}
}
