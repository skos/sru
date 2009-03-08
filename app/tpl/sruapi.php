<?
/**
 * szablon api sru
 */
class UFtpl_SruApi
extends UFtpl_Common {

	public function configDhcp(array $d) {
		$d['computers']->write('configDhcp');
	}

	public function configDnsRev(array $d) {
		$d['computers']->write('configDnsRev');
	}

	public function dnsDs(array $d) {
		$d['computers']->write('configDns');
	}

	public function dnsAdm(array $d) {
		$d['computers']->write('configDns');
	}

	public function ethers(array $d) {
		$d['computers']->write('configEthers');
	}

	public function error404() {
		header('HTTP/1.0 404 Not Found');
	}

	public function penaltiesPast(array $d) {
		$d['penalties']->write('apiPast');
	}

	public function computersLocations(array $d) {
		$d['computers']->write('apiComputersLocations');
	}
}
