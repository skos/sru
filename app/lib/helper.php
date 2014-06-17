<?
/**
 * Pomocnik
 */
class UFlib_Helper {
	public static function removeSpecialChars($string) {
		$unPretty = array('/ä/', '/ö/', '/ü/', '/Ä/', '/Ö/', '/Ü/', '/ß/',
		    '/ą/', '/Ą/', '/ć/', '/Ć/', '/ę/', '/Ę/', '/ł/', '/Ł/', '/ń/', '/Ń/', '/ó/', '/Ó/', '/ś/', '/Ś/', '/ź/', '/Ź/', '/ż/', '/Ż/',
		    '/Š/', '/Ž/', '/š/', '/ž/', '/Ÿ/', '/Ŕ/', '/Á/', '/Â/', '/Ă/', '/Ä/', '/Ĺ/', '/Ç/', '/Č/', '/É/', '/Ę/', '/Ë/', '/Ě/', '/Í/', '/Î/', '/Ď/', '/Ń/',
		    '/Ň/', '/Ó/', '/Ô/', '/Ő/', '/Ö/', '/Ř/', '/Ů/', '/Ú/', '/Ű/', '/Ü/', '/Ý/', '/ŕ/', '/á/', '/â/', '/ă/', '/ä/', '/ĺ/', '/ç/', '/č/', '/é/', '/ę/',
		    '/ë/', '/ě/', '/í/', '/î/', '/ď/', '/ń/', '/ň/', '/ó/', '/ô/', '/ő/', '/ö/', '/ř/', '/ů/', '/ú/', '/ű/', '/ü/', '/ý/', '/˙/',
		    '/Ţ/', '/ţ/', '/Đ/', '/đ/', '/ß/', '/Œ/', '/œ/', '/Ć/', '/ć/', '/ľ/', '/%/', '/</', '/>/', '/&/', '/;/', '/\//', '/\(/', '/\)/');

		$pretty = array('ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue', 'ss',
		    'a', 'A', 'c', 'C', 'e', 'E', 'l', 'L', 'n', 'N', 'o', 'O', 's', 'S', 'z', 'Z', 'z', 'Z',
		    'S', 'Z', 's', 'z', 'Y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N',
		    'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e',
		    'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y',
		    'TH', 'th', 'DH', 'dh', 'ss', 'OE', 'oe', 'AE', 'ae', 'u', '', '', '', '', '', '');

		return strtolower(preg_replace($unPretty, $pretty, nl2br(htmlspecialchars(trim($string)))));
	}

	public static function displayHint($string, $breakLine = true) {
		$length = strlen($string);
		$string = htmlentities($string, ENT_QUOTES, 'UTF-8');
		$returnString = ' <img src="' . UFURL_BASE . '/i/img/pytajnik.png" alt="?" title="' . $string . '" />';
		if ($breakLine) {
			$returnString .= '<br/>';
		}
		return $returnString;
	}

	/**
	 * @param type $array Tablica wejsciowa
	 * @param type $id szukana wartosc srodkowego elementu
	 * @param type $field nazwa klucza w tablicy wejsciowej gdzie szukamy, domyslnie 'id'
	 * 
	 * @return Tablica zawierajaca 3 elementy (lewy, srodkowy, prawy), jeśli srodkowy nie posiada lewego lub prawego, zamiast nich wstawia nulle
	 */
	public static function getLeftRight($bean, $id, $field = 'id') {
		$left = null;
		$middle = null;
		$right = null;
		if ($bean->valid()) {
			$left = $bean->current();
		}
		if ($left['id'] == $id) {//brak lewego
			$left = null;
			$bean->next();
			if ($bean->valid()) {
				$right = $bean->current();
			}
		} else {
			$bean->next();
			if ($bean->valid()) {
				$middle = $bean->current();
			}
			while (true) {
				$right = null;
				$bean->next();
				if ($bean->valid()) {
					$right = $bean->current();
				}
				if ($id == $middle['id']) {
					break;
				}
				$left = $middle;
				$middle = $right;
			}
		}

		return array($left, $middle, $right);
	}

	public static function removePenaltyFromPort($userId) {
		$conf = UFra::shared('UFconf_Sru');
		$penalties = UFra::factory('UFbean_SruAdmin_Penalty');
		try {
			$penaltyList = $penalties->getAllActiveByUserId($userId);
		} catch (Exception $e) {
			$penaltyList = array();
		}

		foreach ($penaltyList as $penalty) {
			$port = UFra::factory('UFbean_SruAdmin_SwitchPort');
			$portData = $port->getByPenaltyUserId($penalty['id'], $userId);

			foreach ($portData as $swPort) {
				if ($swPort['switchId'] > 0 && $swPort['ordinalNo'] > 0 && $swPort['portId'] > 0) {
					$switch = UFra::factory('UFbean_SruAdmin_Switch');
					$switch->getByPK($swPort['switchId']);
					$hp = UFra::factory('UFlib_Snmp_Hp', $switch->ip, $switch);
					$hp->setPortStatus($swPort['ordinalNo'], UFlib_Snmp_Hp::ENABLED);
					$name = UFlib_Helper::formatPortName($swPort['locationAlias'], null, false, $hp->removeSpecialChars($swPort['comment']));
					$hp->setPortAlias($swPort['ordinalNo'], $name);
				}
			}

			try {
				UFra::factory('UFbean_SruAdmin_SwitchPort')->erasePenalty($penalty['id']);
			} catch (Exception $e) {
				
			}
		}
	}

	/**
	 * Formatuje opis portu
	 * @param type $locationAlias
	 * @param type $connectedSwitch
	 * @param type $ban
	 * @param type $comment
	 * @return string
	 */
	public static function formatPortName($locationAlias, $connectedSwitch, $ban, $comment) {
		$conf = UFra::shared('UFconf_Sru');
		
		if ($locationAlias != '') {
			$name = $locationAlias;
			if ($ban) {
				$name .= ': ' . $conf->penaltyPrefix;
			}
			if (!is_null($comment) && $comment != '') {
				$name .= ($ban ? '' : ': ') . $comment;
			}
		} else if (!is_null ($connectedSwitch)) {
			$name = $connectedSwitch->dormitoryAlias . '-hp' . $connectedSwitch->hierarchyNo;
			if (!is_null($comment) && $comment != '') {
				$name .= ': ' . $comment;
			}
		} else if (!is_null($comment) && $comment != '') {
			$name = $comment;
		} else {
			$name = '';
		}
		
		$name = substr($name, 0, UFlib_Snmp_Hp::MAX_PORT_NAME);

		return $name;
	}

	/**
	 * Funkcja konwertująca sekundy do bardziej czytelnej formy
	 * 
	 * @param uint $seconds licza sekund do konwersji
	 * @return mixed 
	 */
	public static function secondsToTime($seconds){
	    $minute = 60;
	    $hour = $minute * 60;
	    $day = $hour * 24;
	    
	    if ($seconds < 0){
		return 0;
	    }
	    
	    $tmp = $seconds;
	    $days = floor($seconds / $day);
	    $tmp %= $day;
	    $hours = floor($tmp / $hour);
	    $tmp %= $hour;
	    $minutes = floor($tmp / $minute);
	    $secs = $tmp % $minute;
	    
	    $time = sprintf("%02d", $hours) . ":" . sprintf("%02d", $minutes) . ":" . sprintf("%02d", $secs);
	    
	    $fullTime = $days . ":" . $time;
	    
	    if($seconds < $day){
		return $time;
	    }else{
		return $fullTime;
	    }
	}
}
