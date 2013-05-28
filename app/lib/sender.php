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
		$this->sendMail($user->email, $title, $body, $action, isset($user->lang) ? $user->lang : null);
		if ($user->gg != '') {
			$this->sendGG($user->gg, $body, isset($user->lang) ? $user->lang : null);
		}
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

	public function sendGG($gg, $text, $lang) {
		require_once 'XMPPHP/XMPP.php';
		$conf = UFra::shared('UFconf_Sru');
		$conn = new XMPPHP_XMPP($conf->jabberServer, $conf->jabberPort, $conf->jabberUser, $conf->jabberPassword, $conf->jabberResource, $conf->jabberDomain, $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
		if ($lang == 'en') {
			$text .= $this->getGgFooterEnglish();
		} else {
			$text .= $this->getGgFooterPolish();
		}

		try {
			$conn->useEncryption(false);
			$conn->connect();
			$conn->processUntil('session_start');
			$conn->presence();
			$conn->message($gg.'@'.$conf->ggGate, $text);
			$conn->disconnect();
		} catch(XMPPHP_Exception $e) {
			die($e->getMessage());
		} 
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

	private function getGgFooterPolish() {
		$footer = "\n".'-- '."\n";
		$footer .= 'Pozdrawiamy,'."\n";
		$footer .= 'Administratorzy SKOS PG'."\n";
		$footer .= 'SRU: https://sru.ds.pg.gda.pl/'."\n";
		$footer .= 'Strona domowa: http://skos.ds.pg.gda.pl/'."\n";
		$footer .= '[wiadomość została wygenerowana automatycznie]'."\n";
		return $footer;
	}

	private function getGgFooterEnglish() {
		$footer = "\n".'-- '."\n";
		$footer .= 'Regards,'."\n";
		$footer .= 'SKOS PG Administrators'."\n";
		$footer .= 'SRU: https://sru.ds.pg.gda.pl/'."\n";
		$footer .= 'Home Page: http://skos.ds.pg.gda.pl/'."\n";
		$footer .= '[this message was generated automatically]'."\n";
		return $footer;
	}
}
