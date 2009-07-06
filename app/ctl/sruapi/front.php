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
				case 'dormitories':
					if ($segCount>1) {
						$get->dormAlias = $req->segment(2);
						if ($segCount>2) {
							switch ($req->segment(3)) {
								case 'computers':
									$get->view = 'dormitory/computers';
									break;
							}
						}
					}
					break;
				case 'computers':
					if ($segCount > 1) {
						switch ($req->segment(2)) {
							case 'outdated':
								$get->view = 'computers/outdated';
								break;
							case 'available':
								// zmiana max czasu rejestracji
								if ($segCount > 2) {
									$get->view = 'computer/changeAvailable';
									$get->availableMaxTo = $req->segment(3);
								}
								break;
						}
					}
					break;
				case 'computer':
					if ($segCount > 1) {
						switch ($req->segment(2)) {
							default:
								// pojedynczy komputer - deaktywacja
								$get->view = 'computer';
								$get->computerHost = $req->segment(2);
								break;
						}
					}
			}
		}
	}

	protected function chooseAction($action = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$post = $req->post;
		$acl = $this->_srv->get('acl');

		if ('penalty' == $get->view && $req->server->is('REQUEST_METHOD') && 'DELETE' == $req->server->REQUEST_METHOD && $acl->sruApi('penalty', 'amnesty')) {
			$act = 'Penalty_Amnesty';
		} elseif ('computer' == $get->view && 'DELETE' == $req->server->REQUEST_METHOD && $acl->sruApi('computer', 'edit')) {
			$act = 'Computer_Deactivate';
		} elseif ('computer/changeAvailable' == $get->view && $acl->sruApi('computer', 'edit')) {
			$act = 'Computer_ChangeAvailable';
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
			case 'computer/changeAvailable':
				if ($msg->get('computer/changeAvailable/ok')) {
					return 'SruApi_Status200';
				} elseif ($msg->get('computer/changeAvailable/error')) {
					return 'SruApi_Error403';
				} else {
					return 'SruApi_Error404';
				}
			case 'computer':
				if ($msg->get('computerDel/ok')) {
					return 'SruApi_Status200';
				} elseif ($msg->get('computerDel/error')) {
					return 'SruApi_Error403';
				} else {
					return 'SruApi_Error404';
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
			case 'dormitory/computers':
				if ($acl->sruApi('computer', 'showLocations')) {
					return 'SruApi_ComputersLocations';
				} else {
					return 'SruApi_Error403';
				}
			case 'computers/outdated':
				if ($acl->sruApi('computer', 'show')) {
					return 'SruApi_ComputersOutdated';
				} else {
					return 'SruApi_Error403';
				}
			default:
				return 'SruApi_Error404';
		}
	}
}
