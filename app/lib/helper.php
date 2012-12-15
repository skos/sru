<?
/**
 * Pomocnik
 */
class UFlib_Helper {
	public static function removeSpecialChars($string) {
		$unPretty = array('/ä/', '/ö/', '/ü/', '/Ä/', '/Ö/', '/Ü/', '/ß/',
			'/ą/', '/Ą/', '/ć/', '/Ć/', '/ę/', '/Ę/', '/ł/', '/Ł/' ,'/ń/', '/Ń/', '/ó/', '/Ó/', '/ś/', '/Ś/', '/ź/', '/Ź/', '/ż/', '/Ż/',
			'/Š/','/Ž/','/š/','/ž/','/Ÿ/','/Ŕ/','/Á/','/Â/','/Ă/','/Ä/','/Ĺ/','/Ç/','/Č/','/É/','/Ę/','/Ë/','/Ě/','/Í/','/Î/','/Ď/','/Ń/',
			'/Ň/','/Ó/','/Ô/','/Ő/','/Ö/','/Ř/','/Ů/','/Ú/','/Ű/','/Ü/','/Ý/','/ŕ/','/á/','/â/','/ă/','/ä/','/ĺ/','/ç/','/č/','/é/','/ę/',
			'/ë/','/ě/','/í/','/î/','/ď/','/ń/','/ň/','/ó/','/ô/','/ő/','/ö/','/ř/','/ů/','/ú/','/ű/','/ü/','/ý/','/˙/',
			'/Ţ/','/ţ/','/Đ/','/đ/','/ß/','/Œ/','/œ/','/Ć/','/ć/','/ľ/','/%/','/</','/>/','/&/','/;/','/\//','/\(/','/\)/');

		$pretty   = array('ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue', 'ss',
			'a', 'A', 'c', 'C', 'e', 'E', 'l', 'L', 'n', 'N', 'o', 'O', 's', 'S', 'z', 'Z', 'z', 'Z',
			'S','Z','s','z','Y','A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','N',
			'O','O','O','O','O','O','U','U','U','U','Y','a','a','a','a','a','a','c','e','e','e',
			'e','i','i','i','i','n','o','o','o','o','o','o','u','u','u','u','y','y',
			'TH','th','DH','dh','ss','OE','oe','AE','ae','u','','','','','','');

		return strtolower(preg_replace($unPretty, $pretty, nl2br(htmlspecialchars(trim($string)))));
	}
	
	public static function displayHint($string, $breakLine = true) {
		$length = strlen($string); 
		$string = htmlentities($string, ENT_QUOTES);
		$returnString = ' <img src="'.UFURL_BASE.'/i/img/pytajnik.png" alt="?" title="'.$string.'" />';
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
	public static function getLeftRight($bean, $id, $field = 'id'){
		$left = null;
		$middle = null;
		$right = null;
		if($bean->valid()){
			$left = $bean->current();
		}
		if($left['id'] == $id){//brak lewego
			$left = null;
			$bean->next();
			if($bean->valid()){
				$right = $bean->current();
			}
		}else{
			$bean->next();
			if($bean->valid()){
				$middle = $bean->current();
			}
			while(true){
				$right = null;
				$bean->next();
				if($bean->valid()){
					$right = $bean->current();
				}
				if($id == $middle['id']){
					break;
				}
				$left = $middle;
				$middle = $right;
			}
		}

		return array($left, $middle, $right);
	}
	
	public static function removePenaltyFromPort($userId){
		$conf = UFra::shared('UFconf_Sru');
		$penalties = UFra::factory('UFbean_SruAdmin_Penalty');
		try{
			$penaltyList = $penalties->getAllActiveByUserId($userId);
		}catch(Exception $e){
			$penaltyList = array();
		}
		
		foreach($penaltyList as $penalty){
			$port = UFra::factory('UFbean_SruAdmin_SwitchPort');
			$portData = $port->getByPenaltyUserId($penalty['id'], $userId);
		
			if($portData[0]['switchId'] > 0 && $portData[0]['ordinalNo'] > 0 && $portData[0]['portId'] > 0){
				$switch = UFra::factory('UFbean_SruAdmin_Switch');
				$switch->getByPK($portData[0]['switchId']);
				$hp = UFra::factory('UFlib_Snmp_Hp', $switch->ip, $switch);
				$hp->setPortStatus($portData[0]['ordinalNo'], UFlib_Snmp_Hp::ENABLED);
				$hp->setPortAlias($portData[0]['ordinalNo'], str_replace($conf->penaltyPrefix, '', $hp->getPortAlias($portData[0]['ordinalNo'])));
			}
		
			try{
				UFra::factory('UFbean_SruAdmin_SwitchPort')->erasePenalty($penalty['id']);
			} catch(Exception $e) {
			}
		}
	}
}
