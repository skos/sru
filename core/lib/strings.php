<?php
/**
* obsluga protokolu http
*/
class UFlib_Strings {

	/**
	 * usuwa niedozwolone znaki z tekstu
	 * 
	 * @param string $txt - "zasmiecony" tekst
	 * @param string $chars - lista dozwolonych znakow (zapis perlowy)
	 * @param string $replace - na co zamienic niedozwolone znaki
	 * @param string $encoding - kodowanie zrodlowego tekstu
	 * @return string
	 */
	public static function filter($txt, $chars='a-zA-Z0-9', $replace='-', $encoding='utf8') {
		return preg_replace('/[^'.$chars.']+/', '-', self::recode($txt, 'utf8', 'flat'));
	}

	public static function recode($txt, $from, $to) {
		if (function_exists('recode_string')) {
			return recode_string($from.'..'.$to, $txt);
		} else {
			if ('flat' == $to) {
				$to = 'us-ascii';
			}
			return iconv($from, $to.'//TRANSLIT', $txt);
		}
	}
}
