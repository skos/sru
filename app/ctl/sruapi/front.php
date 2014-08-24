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
							case 'sendTimeline':
								$get->view = 'penalties/timeline';
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
							case 'serv':
								$get->view = 'dhcp/serv';
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
								$get->ipClass = urldecode($req->segment(2));
								break;
						}
					}
					break;
				case 'ethers':
					$get->view = 'ethers';
					break;
				case 'skosethers':
					$get->view = 'skosethers';
					break;
				case 'admins':
					$get->view = 'admins';
						if($segCount > 1) {
							switch($req->segment(2)) {
								case 'ex':
									$get->view = 'admins/exadmins';
									break;
								case 'outdated':
									$get->view = 'admins/outdated';
									break;
								case 'delete':
									$get->view = 'admins/delete';
									break;
								default:
									break;
							}
						}
					break;
				case 'tourists':
					$get->view = 'tourists';
					break;
				case 'switches':
					$get->view = 'switches';
					if ($segCount > 1) {
						switch ($req->segment(2)) {
							case 'findmac':
								if ($segCount > 2) {
									$get->view = 'switches/findMac';
									$get->mac = $req->segment(3);
								}
								break;
							case 'structure':
								$get->view = 'switches/structure';
								if ($segCount > 2) {
									$get->dormAlias = $req->segment(3);
								}
								break;
							case 'modelips':
								if ($segCount > 2) {
									$get->view = 'switches/modelips';
									$get->model = $req->segment(3);
								}
								break;
							case 'models':
								$get->view = 'switches/models';
								if ($segCount > 2) {
									$get->ds = $req->segment(3);
								}
								break;
						}
					}
					break;
				case 'dormitories':
					if ($segCount>1) {
						$get->dormAlias = $req->segment(2);
						if ($segCount>2) {
							switch ($req->segment(3)) {
								case 'computers':
									$get->view = 'dormitory/computers';
									break;
								case 'freeips':
									$get->view = 'dormitory/freeips';
									break;
								case 'ips':
									$get->view = 'dormitory/ips';
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
							case 'notseen':
								$get->view = 'computers/notseen';
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
					break;
				case 'dutyhours':
					$get->view = 'dutyhours/all';
					if ($segCount > 2) {
						switch ($req->segment(2)) {
							case 'days':
								$get->view = 'dutyhours/upcoming';
								$get->days = (int)$req->segment(3);
								break;
						}
					}
					break;
				case 'firewallexceptions':
					$get->view = 'firewallexceptions';
					break;
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
		} elseif ('penalties/timeline' == $get->view) {
			$act = 'Penalty_Timeline';
		} elseif ('admins/delete' == $get->view && 'DELETE' == $req->server->REQUEST_METHOD && $acl->sruApi('admin', 'delete')) {
			$act = 'Admin_Deactivate';
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
			case 'computer':
				if ($msg->get('computerDeactivate/ok')) {
					return 'SruApi_Status200';
				} elseif ($msg->get('computerDeactivate/error')) {
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
			case 'penalties/timeline':
				if ($msg->get('penaltyTimeline/ok')) {
					return 'SruApi_Status200';
				} elseif ($msg->get('penaltyTimeline/error')) {
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
			case 'dhcp/serv':
				return 'SruApi_DhcpServ';
			case 'dns/ds':
				return 'SruApi_DnsDs';
			case 'dns/adm':
				return 'SruApi_DnsAdm';
			case 'dns':
				return 'SruApi_DnsRev';
			case 'ethers':
				return 'SruApi_Ethers';
			case 'skosethers':
				return 'SruApi_SkosEthers';
			case 'admins':
				return 'SruApi_Admins';
			case 'admins/exadmins':
				return 'SruApi_ExAdmins';
			case 'tourists':
				return 'SruApi_Tourists';
			case 'switches':
				return 'SruApi_Switches';
			case 'switches/findMac':
				return 'SruApi_FindMac';
			case 'switches/structure':
				return 'SruApi_SwitchesStructure';
			case 'switches/modelips':
				return 'SruApi_SwitchesModelIps';
			case 'switches/models':
				return 'SruApi_SwitchesModels';
			case 'dormitory/computers':
				if ($acl->sruApi('computer', 'showLocations')) {
					return 'SruApi_ComputersLocations';
				} else {
					return 'SruApi_Error403';
				}
			case 'dormitory/freeips':
				return 'SruApi_DormitoryFreeIps';
			case 'dormitory/ips':
				return 'SruApi_DormitoryIps';
			case 'computers/outdated':
				if ($acl->sruApi('computer', 'show')) {
					return 'SruApi_ComputersOutdated';
				} else {
					return 'SruApi_Error403';
				}
			case 'computers/notseen':
				if ($acl->sruApi('computer', 'show')) {
					return 'SruApi_ComputersNotSeen';
				} else {
					return 'SruApi_Error403';
				}
				/*if ($acl->sruApi('computer', 'edit')) {
					if($msg->get('computersDelete/ok')) {
						return 'SruApi_Status200';
					} elseif($msg->get('computersDelete/error')) {
						return 'SruApi_Error403';
					} else {
						return 'SruApi_Error404';
					}
				} else {
					return 'SruApi_Error403';
				}*/
			case 'admins/delete':
				if($acl->sruApi('admin', 'delete')){
					if($msg->get('adminsDelete/ok')) {
						return 'SruApi_Status200';
					} elseif($msg->get('adminsDelete/error')) {
						return 'SruApi_Error403';
					} else {
						return 'SruApi_Error404';
					}
				} else {
					return 'SruApi_Error403';
				}
			case 'dutyhours/all':
				return 'SruApi_DutyHoursAll';
			case 'dutyhours/upcoming':
				return 'SruApi_DutyHoursUpcoming';
			//czeka na lepsze czasy - okazało się na razie niepotrzebne
			/*case 'admins/outdated':
				if( $acl->sruApi('admin', 'show')) {
					return 'SruApi_AdminsOutdated';
				} else {
					return 'SruApi_Error403';
				}*/
			case 'firewallexceptions':
				return 'SruApi_FirewallExceptions';
			default:
				return 'SruApi_Error404';
		}
	}
}
