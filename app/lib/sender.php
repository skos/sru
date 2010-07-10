<?
/**
 * Wysyłanie wiadomości
 */
class UFlib_Sender {
	protected function mailHeaders($headers = array()) {
		$mailHeaders = 'MIME-Version: 1.0'."\n";
		$mailHeaders .= 'Content-Type: text/plain; charset=UTF-8'."\n";
		$mailHeaders .=  'Content-Transfer-Encoding: 8bit'."\n";
		$mailHeaders .=  'From: Administratorzy SKOS <adnet@ds.pg.gda.pl>'."\n";
		foreach ($headers as $header => $value) {
			$mailHeaders .=  $header.': '.$value."\n";
		}
		return $mailHeaders;
	}

	// wysyłanie wszystkich powiadomień
	public function send($user, $title, $body, $action = null) {
		$this->sendMail($user->email, $title, $body, $action, $user->lang);
	}

	// wysyłanie maili
	public function sendMail($email, $title, $body, $action = null, $lang = null) {
		if ($action != null) {
			$headers = $this->mailHeaders(array('X-SRU'=>$action));
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
		$footer = '-- '."\n";
		$footer .= 'Pozdrawiamy,'."\n";
		$footer .= 'Administratorzy SKOS PG'."\n";
		$footer .= 'http://skos.ds.pg.gda.pl/'."\n";
		$footer .= '[wiadomość została wygenerowana automatycznie]'."\n";
		return $footer;
	}

	private function getMailFooterEnglish() {
		$footer = '-- '."\n";
		$footer .= 'Regards,'."\n";
		$footer .= 'SKOS PG Administrators'."\n";
		$footer .= 'http://skos.ds.pg.gda.pl/'."\n";
		$footer .= '[this message was generated automatically]'."\n";
		return $footer;
	}
}
