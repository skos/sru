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
}
