<?php

/**
 * Obsługa OTRS
 */
class UFlib_Otrs {

	public function getOpenTickets() {
		$conf = UFra::shared('UFconf_Sru');
		$url = $conf->otrsUrl . '/rpc.pl';
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

	public function sendMessage($user, $message) {
		$conf = UFra::shared('UFconf_Sru');
		$url = $conf->otrsUrl . '/rpc.pl';
		$username = $conf->otrsUser;
		$password = $conf->otrsPass;
		$queue = $conf->otrsQueue;
		$title = "Zgłoszenie wysłane przez SRU";

		$client = new SoapClient(null, array(
		    'location' => $url,
		    'uri' => "Core",
		    'trace' => 1,
		    'login' => $username,
		    'password' => $password,
		    'style' => SOAP_RPC,
		    'use' => SOAP_ENCODED));

		// pobieramy nr ticketa
		$ticketnumber = $client->__soapCall("Dispatch", array($username, $password, "TicketObject", "TicketCreateNumber"));

		// tworzymy ticketa
		$TicketID = $client->__soapCall("Dispatch", array($username, $password, "TicketObject", "TicketCreate",
		    "TN", $ticketnumber,
		    "Title", $title,
		    "Queue", $queue,
		    "Lock", 'unlock',
		    "Priority", '3 normal',
		    "State", 'new',
		    "Type", 'default',
		    "CustomerUser", $user->email,
		    "CustomerID", $user->email,
		    "OwnerID", 1,
		    "ResponsibleID", 1,
		    "UserID", 1
		));
		
		$message = $message."\n\n".'---------------'."\n".'Imię i nazwisko: '.$user->name.' '.$user->surname."\n".'Adres: '.
			$user->dormitoryName.', '.$user->locationAlias."\n".'Zbanowany: '.($user->banned ? 'tak' : 'nie').
			"\n".$conf->sruUrl.'/admin/users/'.$user->id;

		// tworzymy treść ticketa
		$client->__soapCall("Dispatch", array($username, $password,
		    "TicketObject", "ArticleCreate",
		    "TicketID", $TicketID,
		    "ArticleType", "webrequest",
		    "SenderType", "customer",
		    "HistoryType", "WebRequestCustomer",
		    "HistoryComment", "created from PHP",
		    "From", $user->email,
		    "Subject", $title,
		    "ContentType", "text/plain; charset=ISO-8859-2",
		    "Body", $message,
		    "UserID", 1,
		    "Loop", 0,
		    "AutoResponseType", 'auto reply',
		    "OrigHeader", array(
			'From' => $user->email,
			'To' => 'admin@ds.pg.gda.pl',
			'Subject' => $title,
			'Body' => $message
		    ),
		));
	}

}
