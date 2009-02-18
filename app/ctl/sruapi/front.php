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
				case 'penalties':
					if ($segCount>1) {
						switch ($req->segment(2)) {
							case 'past':
								$get->view = 'penalties/past';
								break;
							default:
								$get->view = 'penalty';
								$get->penaltyId = (int)$req->segment(2);
								break;
						}
					}
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

	protected function chooseAction($action = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$post = $req->post;
		$acl = $this->_srv->get('acl');

		if ('penalty' == $get->view && $req->server->is('REQUEST_METHOD') && $req->server->REQUEST_METHOD && $acl->sruApi('penalty', 'amnesty')) {
			$act = 'Penalty_Amnesty';
		}

		if (isset($act)) {
			$action = 'SruApi_'.$act;
		}

		return $action;
	}

	protected function chooseView($view = null) {
		$msg = $this->_srv->get('msg');
		$req = $this->_srv->get('req');
		$acl = $this->_srv->get('acl');
		$get = $req->get;

		switch ($get->view) {
			case 'penalties/past':
				if ($acl->sruApi('penalty', 'show')) {
					return 'SruApi_PenaltiesPast';
				} else {
					return 'SruApi_Error403';
				}
			case 'penalty':
				if ($msg->get('penaltyAmnesty/ok')) {
					return 'SruApi_Status200';
				} elseif ($msg->get('penaltyAmnesty/error')) {
					return 'SruApi_Error403';
				} else {
					return 'SruApi_Error404';
				}
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
