<?php

/**
 * Obsługa API macvendorlookup.com
 */
class UFlib_MacVendorLookup {

	public function getVendor($mac) {
		try {
			$json = file_get_contents('http://www.macvendorlookup.com/api/DwOYLXK/'.urlencode($mac));
			return json_decode($json, true);
		} catch (UFex $e) {
			UFlib_Http::notFound();
			return null;
		}
	}

}
