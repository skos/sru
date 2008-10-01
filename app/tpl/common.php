<?

class UFtpl_Common
extends UFtpl {

	/**
	 * komunikat, ze wszystko jest OK
	 * 
	 * @param string $txt - tresc komunikatu
	 * @return string
	 */
	public function OK($txt) {
		return '<p class="msgOk">'.$txt.'</p>';
	}

	/**
	 * komunikat, ze nastapiil blad
	 * 
	 * @param string $txt - tresc komunikatu
	 * @return string
	 */
	public function ERR($txt) {
		return '<p class="msgError">'.$txt.'</p>';
	}
}
