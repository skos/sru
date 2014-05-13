<?php
/**
 * template
 */
abstract class UFtpl
extends UFlib_ClassWithService {

	const TIME_YYMMDD_HHMMSS='Y-m-d H:i:s';
	const TIME_YYMMDD_HHMM='Y-m-d H:i';
	const TIME_YYMMDD='Y-m-d';

	/**
	 * obiekt obslugi tlumaczen
	 */
	protected $locale;

	public function __construct(&$srv=null) {
		parent::__construct($srv);
		$this->form = UFra::shared('UFlib_Form');
		$this->locale = $this->_chooseLocale();
	}

	/**
	 * wybiera obiekt tlumaczacy
	 * 
	 * @return UFlib
	 */
	protected function _chooseLocale() {
                $session = $this->_srv->get('session');
                if ($session->is('auth') == 1) {
                        setlocale(LC_ALL, 'pl_PL.utf8');
                        putenv("LANG=" . 'pl_PL.utf8');
                        $lang = $session->lang;
                        bindtextdomain($lang,  './../lang');
                        textdomain($lang);
                } else {
                        $lang='';
                        if(array_key_exists('HTTP_ACCEPT_LANGUAGE',$_SERVER))
                                $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
                        switch ($lang) {
                                case "pl":
                                        $lang = 'pl';
                                        break;
                                default:
                                        $lang = 'en';
                                        break;
                        }
                        setlocale(LC_ALL, 'pl_PL.utf8');
                        putenv("LANG=" . 'pl_PL.utf8');
                        bindtextdomain($lang, './../lang');
                        textdomain($lang);
                }
        }

	/**
	 * wypisuje adres url
	 * 
	 * @param int $number - do ktorego segmentu wypisac, liczone wzgl. offsetu
	 * @return string
	 */
	protected function url($number=null) {
		$segments = $this->_srv->get('req')->segments($number);
		return rtrim(UFURL_BASE.'/'.implode('/', $segments), '/');
	}

	/**
	 * escape'uje niebezpieczne znaki
	 * 
	 * @param string $txt - tekst wejsciowy
	 * @return string
	 */
	protected function _escape($txt) {
		return htmlspecialchars($txt);
	}

	/**
	 * czy akcja wykonala sie pomyslnie?
	 * 
	 * @param string $prefix - prefix message'a
	 * @return bool
	 */
	protected function _isOK($prefix) {
		if ($this->_srv->get('msg')->get($prefix.'/ok')) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * czy akcja wykonala sie z bledem?
	 * 
	 * @param string $prefix - prefix message'a
	 * @return bool
	 */
	protected function _isERR($prefix) {
		if ($this->_srv->get('msg')->get($prefix.'/errors')) {
			return true;
		} else {
			return false;
		}
	}

	public function _default($d) {
		UFra::warning('Unknown template used');
	}
}
