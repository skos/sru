<?php

/**
 * Obsługa OTRS
 */
class UFlib_Otrs {

	public function getOpenTickets() {
		$conf = UFra::shared('UFconf_Sru');
		$url = $conf->otrsUrl;
		$username = $conf->otrsUser;
		$password = $conf->otrsPass;
		
		$client = new SoapClient(null, array(
		    'location' => $url,
		    'uri' => "Core",
		    'trace' => 1,
		    'login' => $username,
		    'password' => $password,
		    'style' => SOAP_RPC,
		    'use' => SOAP_ENCODED));

		$tickets = $client->__soapCall("Dispatch", array($username, $password,
		    "TicketObject", "TicketSearch",
		    "UserID", "1", "Result", "ARRAY", "StateType", array("new", "open")
		));

		$allTickets = array();
		foreach ($tickets as $ticketId) {
			$ticketDetails = $client->__soapCall("Dispatch", array($username, $password,
			    "TicketObject", "TicketGet",
			    "TicketID", $ticketId, "Result", "ARRAY"
			));

			$ticketInfo = array();
			$i = 0;
			foreach ($ticketDetails as $name => $value) { // explode the xml response
				if (false !== strpos($name, "s-gensym")) {
					$temp[$i] = $value;
					if ($i % 2 != 0) {
						$v = $temp[$i - 1];
						$ticketInfo[$v] = $value;
					}
					$i++;
				}
			}

			$allTickets[$ticketId] = $ticketInfo;
		}

		return $allTickets;
	}

}