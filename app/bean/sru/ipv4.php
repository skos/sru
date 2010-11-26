<?
/**
 * ip
 */
class UFbean_Sru_Ipv4
extends UFbeanSingle {

	public function getIps() {
		return $this->data[0]['ip'];
	}

}
