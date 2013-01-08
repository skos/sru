<?php
/**
* klasa zapewniajaca tlumaczenie
*/
class UFlib_Locale {

	protected $dict = array();

	/**
	 * konstruktor
	 * 
	 * @param string $domain - nazwa domeny (przewaznie klasy)
	 * @param string $lang - jezyk
	 */
	public function __construct($domain, $lang=null) {
		if (!is_string($lang) || ''===$lang) {
			return;
		}
		$file = UFDIR_APP.'lang/'.$lang.'/LC_MESSAGES/'.$domain.'.php';
		include($file);
		if (isset($dict)) {
			$this->dict =& $dict;
		} else {
			UFra::error('Locale not found - lang: '.$lang.', domain: '.$domain);
		}
	}

	/**
	 * tlumaczy tekst
	 * 
	 * @param string $txt - tekst do przetlumaczenia
	 * @param int $number - krotnosc elementu liczbowego
	 * @return string - przetlumaczony tekst
	 */
	public function _($txt, $number=null) {
		if (isset($this->dict[$txt])) {
			return $this->dict[$txt];
		} else {
			return $txt;
		}
	}
}
