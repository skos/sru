<?
/**
 * Obsługa SNMP
 */
class UFlib_Snmp {
	protected function clearResult($input) {
		$output = str_replace('STRING:', '', $input);
		$output = str_replace('Hex-', '', $output);
		$output = str_replace('INTEGER:', '', $output);
		$output = str_replace('"', "", $output);
		return trim($output);
	}

	protected function clearResults($inputs) {
		for ($i = 0; $i < count($inputs); $i++) {
			$inputs[$i] = $this->clearResult($inputs[$i]);
		}
		return $inputs;
	}

	protected function int2mac($mac) {
		$parts = preg_split('/[^0-9A-Fa-f]/', $mac);
		foreach ($parts as $id=>&$part) {
			$part = dechex($part);
			if (strlen($part) == 1) {
				$part = '0'.$part;
			}
		}
		return substr(implode(':', $parts), 0, -3);
	}

	protected function mac2int($mac) {
		$parts = preg_split('/[^0-9A-Fa-f]/', $mac);
		foreach ($parts as $id=>&$part) {
			$part = hexdec($part);
		}
		return implode('.', $parts);
	}

	public function removeSpecialChars($string) {
		$unPretty = array('/ä/', '/ö/', '/ü/', '/Ä/', '/Ö/', '/Ü/', '/ß/',
			'/ą/', '/Ą/', '/ć/', '/Ć/', '/ę/', '/Ę/', '/ł/', '/Ł/' ,'/ń/', '/Ń/', '/ó/', '/Ó/', '/ś/', '/Ś/', '/ź/', '/Ź/', '/ż/', '/Ż/',
			'/Š/','/Ž/','/š/','/ž/','/Ÿ/','/Ŕ/','/Á/','/Â/','/Ă/','/Ä/','/Ĺ/','/Ç/','/Č/','/É/','/Ę/','/Ë/','/Ě/','/Í/','/Î/','/Ď/','/Ń/',
			'/Ň/','/Ó/','/Ô/','/Ő/','/Ö/','/Ř/','/Ů/','/Ú/','/Ű/','/Ü/','/Ý/','/ŕ/','/á/','/â/','/ă/','/ä/','/ĺ/','/ç/','/č/','/é/','/ę/',
			'/ë/','/ě/','/í/','/î/','/ď/','/ń/','/ň/','/ó/','/ô/','/ő/','/ö/','/ř/','/ů/','/ú/','/ű/','/ü/','/ý/','/˙/',
			'/Ţ/','/ţ/','/Đ/','/đ/','/ß/','/Œ/','/œ/','/Ć/','/ć/','/ľ/');

		$pretty   = array('ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue', 'ss',
			'a', 'A', 'c', 'C', 'e', 'E', 'l', 'L', 'n', 'N', 'o', 'O', 's', 'S', 'z', 'Z', 'z', 'Z',
			'S','Z','s','z','Y','A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','N',
			'O','O','O','O','O','O','U','U','U','U','Y','a','a','a','a','a','a','c','e','e','e',
			'e','i','i','i','i','n','o','o','o','o','o','o','u','u','u','u','y','y',
			'TH','th','DH','dh','ss','OE','oe','AE','ae','u');

		return strtolower(preg_replace($unPretty, $pretty, trim($string)));
    }
}
