<?
/**
 * front controller api
 */
class UFctl_SruApi_Front
extends UFctl {

	protected function parseParameters() {
		$req = $this->_srv->get('req');
		$get = $req->get;
		
		$segCount = $req->segmentsCount();
		$get->view = '-';
		if ($segCount>0) {
			switch ($req->segment(1)) {
				case 'dhcp':
					if ($segCount>1) {
						switch ($req->segment(2)) {
							case 'stud':
								$get->view = 'dhcp/stud';
								break;
							case 'adm':
								$get->view = 'dhcp/adm';
								break;
							case 'org':
								$get->view = 'dhcp/org';
								break;
						}
					}
					break;
				case 'dns':
					if ($segCount>1) {
						switch ($req->segment(2)) {
							case 'ds':
								$get->view = 'dns/ds';
								break;
							case 'adm':
								$get->view = 'dns/adm';
								break;
							default:
								$get->view = 'dns';
								$get->ipClass = (int)$req->segment(2);
								break;
						}
					}
					break;
				case 'ethers':
					$get->view = 'ethers';
					break;
			}
		}
	}

	protected function chooseView($view = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;

		switch ($get->view) {
			case 'dhcp/stud':
				return 'SruApi_DhcpStuds';
			case 'dhcp/adm':
				return 'SruApi_DhcpAdm';
			case 'dhcp/org':
				return 'SruApi_DhcpOrg';
			case 'dns/ds':
				return 'SruApi_DnsDs';
			case 'dns/adm':
				return 'SruApi_DnsAdm';
			case 'dns':
				return 'SruApi_DnsRev';
			case 'ethers':
				return 'SruApi_Ethers';
			default:
				return 'SruApi_Error404';
		}
	}
}
