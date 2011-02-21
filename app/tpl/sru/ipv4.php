<?
/**
 * szablon IPv4
 */
class UFtpl_Sru_Ipv4
extends UFtpl_Common {
	public function apiDormitoryIps(array $d, $used) {
		echo ($d['0']['ip'] > 0 ? round(100*$used->getIpCount()/$d['0']['ip']) : 0).'% '.$used->getIpCount().'/'.$d['0']['ip'].' (used_IP/available_IP)';
	}
}
