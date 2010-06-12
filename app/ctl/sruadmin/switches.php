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
				default:
					$get->view = 'switches/switch';
					$id = (int)$req->segment(2);
					if ($id <= 0) {
						$get->view = 'error404';
						break;
					}
					$get->switchId = $id;

					if ($segCount > 2) {
						switch ($req->segment(3)) {
							case 'tech':
								$get->view = 'switches/tech';
								break;
							case ':edit':
								$get->view = 'switches/edit';
								break;
							case ':lockoutsedit':
								$get->view = 'switches/lockoutsedit';
								break;
							case ':portsedit':
								$get->view = 'switches/portsedit';
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
											$get->portId = $id;

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
		} elseif ('switches/edit' == $get->view && $post->is('switchEdit') && $acl->sruAdmin('switch', 'edit', $get->switchId)) {
			$act = 'Switch_Edit';
		} elseif ('switches/lockoutsedit' == $get->view && $post->is('switchLockoutsEdit') && $acl->sruAdmin('switch', 'edit', $get->switchId)) {
			$act = 'SwitchLockouts_Edit';
		} elseif ('switches/portsedit' == $get->view && $post->is('switchPortsEdit') && $acl->sruAdmin('switch', 'edit', $get->switchId)) {
			$act = 'SwitchPorts_Edit';
		} elseif ('port/edit' == $get->view && $post->is('switchPortEdit') && $acl->sruAdmin('switch', 'edit', $get->switchId)) {
			$act = 'SwitchPort_Edit';
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
				} elseif ($acl->sruAdmin('switch', 'edit', $get->switchId)) {
					return 'SruAdmin_SwitchEdit';
				} else {
					return 'Sru_Error403';
				}
			case 'switches/lockoutsedit':
				if ($msg->get('switchLockoutsEdit/ok')) { 
					return 'SruAdmin_Switch';
				} elseif ($acl->sruAdmin('switch', 'edit', $get->switchId)) {
					return 'SruAdmin_SwitchLockoutsEdit';
				} else {
					return 'Sru_Error403';
				}
			case 'switches/portsedit':
				if ($msg->get('switchPortsEdit/ok')) { 
					return 'SruAdmin_Switch';
				} elseif ($acl->sruAdmin('switch', 'edit', $get->switchId)) {
					return 'SruAdmin_SwitchPortsEdit';
				} else {
					return 'Sru_Error403';
				}
			case 'port/main':
				return 'SruAdmin_SwitchPort';
			case 'port/macs':
				return 'SruAdmin_SwitchPortMacs';
			case 'port/edit':
				if ($msg->get('switchPortEdit/ok')) { 
					return 'SruAdmin_SwitchPort';
				} elseif ($acl->sruAdmin('switch', 'edit', $get->switchId)) {
					return 'SruAdmin_SwitchPortEdit';
				} else {
					return 'Sru_Error403';
				}
			default:
				return 'Sru_Error404';
		}
	}
}