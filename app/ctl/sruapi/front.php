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
					break;
				case 'dhcp':
					if ($segCount>1) {
						$get->view = 'dhcp';
						$get->domain = urldecode($req->segment(2));
						break;
					}
					break;
				case 'dns':
					if ($segCount>1) {
						switch ($req->segment(2)) {
							case 'rev':
								if ($segCount > 2) {
									$get->view = 'revdns';
									$get->ipClass = urldecode($req->segment(3));
									break;
								} else {
									break;
								}
							default:
								$get->view = 'dns';
								$get->domain = urldecode($req->segment(2));
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
							case 'servers':
								$get->view = 'computers/servers';
						}
					}
					break;
				case 'computer':
					if ($segCount > 1) {
						switch ($req->segment(2)) {
							default:
								// pojedynczy komputer - dezaktywacja
								$get->view = 'computer';
								$get->computerHost = $req->segment(2);
								break;
						}
					}
					break;
				case 'users':
					if ($segCount > 1) {
						switch ($req->segment(2)) {
							case 'todeactivate':
								$get->view = 'users/toDeactivate';
								break;
							case 'toremove':
								$get->view = 'users/toRemove';
								break;
						}
					}
					break;
				case 'userdeactivate':
					if ($segCount > 1) {
						switch ($req->segment(2)) {
							default:
								// pojedynczy user - dezaktywacja
								$get->view = 'user/deactivate';
								$get->userId = $req->segment(2);
								break;
						}
					}
					break;
				case 'userremove':
					if ($segCount > 1) {
						switch ($req->segment(2)) {
							default:
								// pojedynczy user - usuniecie
								$get->view = 'user/remove';
								$get->userId = $req->segment(2);
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
					if ($segCount > 1) {
						switch ($req->segment(2)) {
							case 'outdated':
								$get->view = 'firewallexceptions/outdated';
								break;
							default:
								// pojedynczy wyjatek - dezaktywacja
								$get->view = 'firewallexception';
								$get->fwId = $req->segment(2);
								break;
						}
					}
					break;
				case 'validator':
					if ($segCount > 2) {
						$get->validatorTest = urldecode($req->segment(2));
						$get->validatorObject = urldecode($req->segment(3));
						$get->view = 'validator';
					}
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
		} elseif ('user/deactivate' == $get->view && 'DELETE' == $req->server->REQUEST_METHOD && $acl->sruApi('user', 'edit')) {
			$act = 'User_Deactivate';
		} elseif ('user/remove' == $get->view && 'DELETE' == $req->server->REQUEST_METHOD && $acl->sruApi('user', 'edit')) {
			$act = 'User_Remove';
		} elseif ('penalties/timeline' == $get->view) {
			$act = 'Penalty_Timeline';
		} elseif ('admins/delete' == $get->view && 'DELETE' == $req->server->REQUEST_METHOD && $acl->sruApi('admin', 'delete')) {
			$act = 'Admin_Deactivate';
		} elseif ('firewallexception' == $get->view && 'DELETE' == $req->server->REQUEST_METHOD && $acl->sruApi('firewallexceptions', 'edit')) {
			$act = 'Firewallexceptions_Deactivate';
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
			case 'dhcp':
				return 'SruApi_Dhcp';
			case 'dns':
				return 'SruApi_Dns';
			case 'revdns':
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
			case 'computers/servers':
				return 'SruApi_ComputersServers';
			case 'users/toDeactivate':
				if ($acl->sruApi('user', 'show')) {
					return 'SruApi_UsersToDeactivate';
				} else {
					return 'SruApi_Error403';
				}
			case 'users/toRemove':
				if ($acl->sruApi('user', 'show')) {
					return 'SruApi_UsersToRemove';
				} else {
					return 'SruApi_Error403';
				}
			case 'user/deactivate':
				if ($msg->get('userDeactivate/ok')) {
					return 'SruApi_Status200';
				} elseif ($msg->get('userDeactivate/error')) {
					return 'SruApi_Error403';
				} else {
					return 'SruApi_Error404';
				}
			case 'user/remove':
				if ($msg->get('userRemove/ok')) {
					return 'SruApi_Status200';
				} elseif ($msg->get('userRemove/error')) {
					return 'SruApi_Error403';
				} else {
					return 'SruApi_Error404';
				}
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
			case 'firewallexception':
				if ($msg->get('fwExceptionDeactivate/ok')) {
					return 'SruApi_Status200';
				} elseif ($msg->get('fwExceptionDeactivate/error')) {
					return 'SruApi_Error403';
				} else {
					return 'SruApi_Error404';
				}
			case 'firewallexceptions':
				return 'SruApi_FirewallExceptions';
			case 'firewallexceptions/outdated':
				return 'SruApi_FirewallExceptionsOutdated';
			case 'validator':
				return 'SruApi_Validator';
			default:
				return 'SruApi_Error404';
		}
	}
}
