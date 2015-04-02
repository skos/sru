<?
/**
 * ip
 */
class UFbean_Sru_Ipv4
extends UFbeanSingle {

	public function getIpCount() {
		return $this->data[0]['ip'];
	}

	public function checkIpDormitory($ip, $dormId){
		return $this->dao->checkIpDormitory($ip, $dormId);
	}
}
