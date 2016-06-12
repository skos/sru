<?php

/**
 * Obsługa API sprawdzania dostawcy MAC
 */
class UFlib_MacVendorLookup {

	public function getVendor($mac) {
		$conf = UFra::shared('UFconf_Sru');

		try {
			$vendor = file_get_contents($conf->macVendorLookupAPIUrl.urlencode($mac));
			if ($conf->macVendorLookupAPIJson) {
				return json_decode($vendor, true);
			} else {
				return htmlspecialchars($vendor);
			}
		} catch (UFex $e) {
			UFlib_Http::notFound();
			return null;
		}
	}

}
