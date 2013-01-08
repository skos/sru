<?php

/**
 * "skrzynka" do przesylania wiadomosci
 */
class UFlib_Messages {

	/**
	 * zdefiniowane wiadomosci
	 */
	protected $data = array();

	/**
	 * tworzy wiadomosc
	 * 
	 * @param string $var - "adres" wiadomosci; jego czlony oddzielone sa "/"
	 * @param mixed $val - tresc wiadomosci
	 * @return bool - czy wiadomosc juz istniala i zostala nadpisana?
	 */
	public function set($var, $val=true) {
		if (is_array($val)) {
			throw UFra::factory('UFex_Core_NoParameter', 'Value can not be an array');
		}
		$keys = explode('/', trim($var, '/'));

		$tab =& $this->data;

		foreach ($keys as $item) {
			if (!isset($tab[$item]) || !is_array($tab[$item])) {
				$tab[$item] = array();
			}
			$tab =& $tab[$item];
		}
		$tab = $val;
	}

	/**
	 * odczytuje wiadomosc
	 * 
	 * @param string $var - "adres" wiadomosci
	 * @return mixed - tresc wiadomosci
	 */
	public function get($var) {
		$tab =& $this->data;

		$keys = explode('/', trim($var, '/'));

		foreach ($keys as $item) {
			if (isset($tab[$item])) {
				$tab =& $tab[$item];
			} else {
				return false;
			}
		}
		if (is_array($tab)) {
			return true;
		} else {
			return $tab;
		}
	}

	/**
	 * kasuje wiadomosc
	 * 
	 * @param string $var - "adres" wiadomosci
	 */
	public function del($var) {
		$this->set($var, false);
	}
}
