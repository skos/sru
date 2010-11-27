<?
/**
 * szablon IPv4
 */
class UFtpl_Sru_Ipv4
extends UFtpl_Common {
	public function apiDormitoryIps(array $d, $used) {
		echo round(100*$used->getIpCount()/$d['0']['ip']).'% '.$used->getIpCount().'/'.$d['0']['ip'].' (used_IP/available_IP)';
	}
}
