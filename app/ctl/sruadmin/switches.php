<?php
/**
 * front controller czesci administracyjnej sru modulu switchy
 */
class UFctl_SruAdmin_Switches
extends UFctl {

	protected function parseParameters() 
	{
		$req = $this->_srv->get('req');
		$get = $req->get;
		$acl = $this->_srv->get('acl');
		
		$segCount = $req->segmentsCount();

		// wyzsze segmenty sprawdzane sa w front'ie
		if (1 == $segCount) {
			$get->view = 'switches/main';
		} else {
			switch ($req->segment(2)) {
				case ':add':
					$get->view = 'switches/add';
					break;
				case 'dorm':
					if (2 == $segCount) {
						$get->view = 'error404';
					} else {
						switch ($req->segment(3)) {
							default:
								$get->view = 'switches/main';
								$id = $req->segment(3);
								$get->dormAlias = $id;
						}
					}
					break;
				case 'getData':
					if (2 == $segCount) {
						$get->view = 'error404';
					} else {
						switch ($req->segment(3)) {
							default:
								$get->view = 'switches/getData';
								$ip = $req->segment(3);
								$get->switchIp = $ip;
						}
					}
					break;
				default:
					$get->view = 'switches/switch';
					$id = $req->segment(2);
					if ($id < 0) {
						$get->view = 'error404';
						break;
					}
					try {
						// jeśli zmieniliśmy SN switcha, musimy pobrać nowy
						$get->switchSn = $get->newSwitchSn;
						unset($get->newSwitchSn);
					} catch (UFex_Core_DataNotFound $e) {
						$get->switchSn = $id;
					}

					if ($segCount > 2) {
						switch ($req->segment(3)) {
							case 'tech':
								$get->view = 'switches/tech';
								break;
							case ':edit':
								$get->view = 'switches/edit';
								break;
							case 'history':
								$get->view = 'switches/history';
								break;
							case 'inventorycardhistory':
								$get->view = 'inventorycard/history';
								break;
							case ':inventorycardedit':
								$get->view = 'inventorycard/edit';
								break;
							case ':lockoutsedit':
								$get->view = 'switches/lockoutsedit';
								break;
							case ':portsedit':
								$get->view = 'switches/portsedit';
								break;
							case ':copyAliases':
								$get->view = 'switches/portsedit';
								$get->copyFromSwitch = true;
								break;
							case 'port':
								if (3 == $segCount) {
									$get->view = 'error404';
								} else {
									switch ($req->segment(4)) {
										default:
											$get->view = 'port/main';
											$id = (int)$req->segment(4);
											if ($id <= 0) {
												$get->view = 'error404';
												break;
											}
											$get->portNo = $id;

											if ($segCount > 4) {
													switch ($req->segment(5)) {
														case 'macs':
															$get->view = 'port/macs';
															break;
														case ':edit':
															$get->view = 'port/edit';
															break;
														default:
															$get->view = 'error404';
															break;
													}
											}
									}
								}
								break;
							default:
								$get->view = 'error404';
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

		if ($post->is('adminLogout') && $acl->sruAdmin('admin', 'logout')) {
			$act = 'Admin_Logout';
		} elseif ($post->is('adminLogin') && $acl->sruAdmin('admin', 'login')) {
			$act = 'Admin_Login';
		} elseif ('switches/add' == $get->view && $post->is('switchAdd') && $acl->sruAdmin('switch', 'add')) {
			$act = 'Switch_Add';
		} elseif ('switches/edit' == $get->view && $post->is('switchEdit') && $acl->sruAdmin('switch', 'edit', $get->switchSn)) {
			$act = 'Switch_Edit';
		} elseif ('switches/lockoutsedit' == $get->view && $post->is('switchLockoutsEdit') && $acl->sruAdmin('switch', 'edit', $get->switchSn)) {
			$act = 'SwitchLockouts_Edit';
		} elseif ('switches/portsedit' == $get->view && $post->is('switchPortsEdit') && $acl->sruAdmin('switch', 'edit', $get->switchSn)) {
			$act = 'SwitchPorts_Edit';
		} elseif ('port/edit' == $get->view && $post->is('switchPortEdit') && $acl->sruAdmin('switch', 'edit', $get->switchSn)) {
			$act = 'SwitchPort_Edit';
		} elseif ('inventorycard/edit' == $get->view && $post->is('inventoryCardEdit') && $acl->sruAdmin('switch', 'edit', $get->switchSn)) {
			$act = 'InventoryCard_Edit';
		}

		if (isset($act)) {
			$action = 'SruAdmin_'.$act;
		}

		return $action;
	}

	protected function chooseView($view = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$post= $req->post;
		$msg = $this->_srv->get('msg');
		$acl = $this->_srv->get('acl');

		if (!$acl->sruAdmin('admin', 'logout')) {
			return 'SruAdmin_Login';
		}
		
		switch ($get->view) 
		{
			case 'switches/main':
				return 'SruAdmin_Switches';
			case 'switches/switch':
				return 'SruAdmin_Switch';
			case 'switches/tech':
				return 'SruAdmin_SwitchTech';
			case 'switches/add':
				if ($msg->get('switchAdd/ok')) {
					return 'SruAdmin_Switches';
				} elseif ($acl->sruAdmin('switch', 'add')) {
					return 'SruAdmin_SwitchAdd';
				} else {
					return 'Sru_Error404';
				}
			case 'switches/edit':
				if ($msg->get('switchEdit/ok')) { 
					return 'SruAdmin_Switch';
				} elseif ($acl->sruAdmin('switch', 'edit', $get->switchSn)) {
					return 'SruAdmin_SwitchEdit';
				} else {
					return 'Sru_Error403';
				}
			case 'switches/history':
				return 'SruAdmin_SwitchHistory';
			case 'switches/lockoutsedit':
				if ($msg->get('switchLockoutsEdit/ok')) { 
					return 'SruAdmin_Switch';
				} elseif ($acl->sruAdmin('switch', 'edit', $get->switchSn)) {
					return 'SruAdmin_SwitchLockoutsEdit';
				} else {
					return 'Sru_Error403';
				}
			case 'switches/portsedit':
				if ($msg->get('switchPortsEdit/ok')) { 
					return 'SruAdmin_Switch';
				} elseif ($acl->sruAdmin('switch', 'edit', $get->switchSn)) {
					return 'SruAdmin_SwitchPortsEdit';
				} else {
					return 'Sru_Error403';
				}
			case 'switches/getData':
				return 'SruAdmin_SwitchData';
			case 'port/main':
				return 'SruAdmin_SwitchPort';
			case 'port/macs':
				return 'SruAdmin_SwitchPortMacs';
			case 'port/edit':
				if ($msg->get('switchPortEdit/ok')) { 
					return 'SruAdmin_SwitchPort';
				} elseif ($acl->sruAdmin('switch', 'edit', $get->switchSn)) {
					return 'SruAdmin_SwitchPortEdit';
				} else {
					return 'Sru_Error403';
				}
			case 'inventorycard/history':
				return 'SruAdmin_SwitchInventoryCardHistory';
			case 'inventorycard/edit':
				if ($msg->get('inventoryCardEdit/ok')) { 
					return 'SruAdmin_Switch';
				} elseif ($acl->sruAdmin('switch', 'edit', $get->switchSn)) {
					return 'SruAdmin_SwitchInventoryCardEdit';
				} else {
					return 'Sru_Error403';
				}
			default:
				return 'Sru_Error404';
		}
	}
}
