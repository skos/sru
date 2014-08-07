<?
/**
 * Wysyłanie wiadomości
 */
class UFlib_Sender {
	protected function mailHeaders($headers = array()) {
		$mailHeaders = 'MIME-Version: 1.0'."\n";
		$mailHeaders .= 'Content-Type: text/plain; charset=UTF-8'."\n";
		$mailHeaders .=  'Content-Transfer-Encoding: 8bit'."\n";
		$mailHeaders .=  'From: Administratorzy SKOS <admin@ds.pg.gda.pl>'."\n";
		foreach ($headers as $header => $value) {
			$mailHeaders .=  $header.': '.$value."\n";
		}
		return $mailHeaders;
	}

	/**
	 * wysyłanie wszystkich powiadomień
	 * @param type $user użytkownik
	 * @param type $title tytuł
	 * @param type $body treść
	 * @param type $action ew. akcja wywołująca maila
	 * @param type $dormitoryAlias akademik, do którego powinna pójść ew. odpowiedź na maila
	 */
	public function send($user, $title, $body, $action = null, $dormitoryAlias = null) {
		$this->sendMail($user->email, $title, $body, $action, isset($user->lang) ? $user->lang : null, $dormitoryAlias);
	}

	/**
	 * Wysyłanie maili
	 * @param type $email adresat
	 * @param string $title tytuł
	 * @param type $body treść
	 * @param type $action ew. akcja wywołująca maila
	 * @param type $lang język
	 * @param type $dormitoryAlias akademik, do którego powinna pójść ew. odpowiedź na maila
	 */
	public function sendMail($email, $title, $body, $action = null, $lang = null, $dormitoryAlias = null) {
		if ($action != null) {
			$additionalHeaders = array();
			$additionalHeaders['X-SRU'] = $action;
			if ($action == UFact_SruWalet_User_Del::PREFIX && !is_null($dormitoryAlias)) {
				$additionalHeaders['Reply-to'] = 'admin@ds.pg.gda.pl; '.$dormitoryAlias.'@pg.gda.pl';
			}
			$headers = $this->mailHeaders($additionalHeaders);
		} else {
			$headers = $this->mailHeaders();
		}
		if ($lang == 'en') {
			$body .= $this->getMailFooterEnglish();
		} else {
			$body .= $this->getMailFooterPolish();
		}
		$conf = UFra::shared('UFconf_Sru');
		$title = $conf->emailPrefix.' '.$title;

		mail($email, '=?UTF-8?B?'.base64_encode($title).'?=', $body, $headers);
	}

	// nagłówki
	private function getMailFooterPolish() {
		$footer = "\n".'-- '."\n";
		$footer .= 'Pozdrawiamy,'."\n";
		$footer .= 'Administratorzy SKOS PG'."\n";
		$footer .= 'SRU: https://sru.ds.pg.gda.pl/'."\n";
		$footer .= 'Strona domowa: http://skos.ds.pg.gda.pl/'."\n";
		$footer .= '[wiadomość została wygenerowana automatycznie]'."\n";
		return $footer;
	}

	private function getMailFooterEnglish() {
		$footer = "\n".'-- '."\n";
		$footer .= 'Regards,'."\n";
		$footer .= 'SKOS PG Administrators'."\n";
		$footer .= 'SRU: https://sru.ds.pg.gda.pl/'."\n";
		$footer .= 'Home Page: http://skos.ds.pg.gda.pl/en/'."\n";
		$footer .= '[this message was generated automatically]'."\n";
		return $footer;
	}
}
