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
		return UFlib_Helper::removeSpecialChars($string);
    }
}
