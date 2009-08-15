<?
/**
 * front controller modulu sru
 */
class UFctl_Sru_Front
extends UFctl {

	protected function parseParameters() {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$acl = $this->_srv->get('acl');

		$segCount = $req->segmentsCount();
		if (0 == $segCount) {
			$get->view = 'user/main';
		} else {
			switch ($req->segment(1)) {
				case 'profile':
					$get->view = 'user/edit';
					break;
				case 'computers':
					if ($segCount > 1) {
						switch ($req->segment(2)) {
							case ':add':
								$get->view = 'user/computer/add';
								break;
							default:
								$get->computerId = (int)$req->segment(2);
								if ($segCount > 2 && ':edit' == $req->segment(3)) {
									$get->view = 'user/computer/edit';
								} else {
									$get->view = 'user/computer';
								}
						}
					} else {
						$get->view = 'user/computers';
					}
					break;
				case 'create':
					$get->view = 'user/add';
					break;
				case 'penalties':
					$get->view = 'user/penalties';
					break;
				default:
					if (UFlib_Valid::regexp($req->segment(1),'^[0-9a-f]{32}$')) {
						$get->userToken = $req->segment(1);
						$get->view = 'user/main';
						break;
					}
					$get->view = 'error404';
					break;
			}
		}
	}

	protected function chooseAction($action = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$post = $req->post;
		$acl = $this->_srv->get('acl');

		if ($post->is('userLogout') && $acl->sru('user', 'logout')) {
			$act = 'User_Logout';
		} elseif ($post->is('userLogin') && $acl->sru('user', 'login')) {
			$act = 'User_Login';
		} elseif ('user/main' == $get->view && $post->is('sendPassword') && $acl->sru('user', 'login')) {
			$act = 'User_SendPassword';
		} elseif ('user/main' == $get->view && $get->is('userToken') && $acl->sru('user', 'add')) {
			$act = 'User_Confirm';
		} elseif ('user/edit' == $get->view && $post->is('userEdit') && $acl->sru('user', 'edit')) {
			$act = 'User_Edit';
		} elseif ('user/add' == $get->view && $post->is('userAdd') && $acl->sru('user', 'add')) {
			$act = 'User_Add';
		} elseif ('user/computer/edit' == $get->view && $post->is('computerEdit') && $acl->sru('computer', 'edit')) {
			$act = 'Computer_Edit';
		} elseif ('user/computer/add' == $get->view && $post->is('computerAdd') && $acl->sru('computer', 'add')) {
			$act = 'Computer_Add';
		} elseif ('user/computer/edit' == $get->view && $post->is('computerDel') && $acl->sru('computer', 'del')) {
			$act = 'Computer_Del';
		}

		if (isset($act)) {
			$action = 'Sru_'.$act;
		}

		return $action;
	}

	protected function chooseView($view = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$post= $req->post;
		$msg = $this->_srv->get('msg');
		$acl = $this->_srv->get('acl');

		switch ($get->view) {
			case 'user/main':
				if ($acl->sru('user', 'login')) {
					return 'Sru_Login';
				} else {
					return 'Sru_UserMain';
				}
			case 'user/add':
				if ($msg->get('userAdd/ok')) {
					return 'Sru_Login';
				} elseif ($acl->sru('user', 'add')) {
					return 'Sru_UserAdd';
				} else {
					return 'Sru_Error404';
				}
			case 'user/edit':
				if ($acl->sru('user', 'edit')) {
					return 'Sru_UserEdit';
				} else {
					return 'Sru_Error403';
				}
			case 'user/computers':
				if ($acl->sru('user', 'logout')) {
					return 'Sru_UserComputers';
				} else {
					return 'Sru_Error403';
				}
			case 'user/computer':
				if ($acl->sru('user', 'logout')) {
					return 'Sru_UserComputer';
				} else {
					return 'Sru_Error403';
				}
			case 'user/computer/add':
				if ($msg->get('computerAdd/ok')) {
					return 'Sru_UserComputers';	
				} elseif ($acl->sru('computer', 'add')) {
					return 'Sru_UserComputerAdd';
				} else {
					return 'Sru_Error403';
				}
			case 'user/computer/edit':
				if ($msg->get('computerDel/ok')) {
					return 'Sru_UserComputers';	
				} elseif ($acl->sru('computer', 'edit')) {
					return 'Sru_UserComputerEdit';
				} else {
					return 'Sru_Error403';
				}
			case 'user/computer/del':
				if ($acl->sru('computer', 'del')) {
					return 'Sru_UserComputerDel';
				} else {
					return 'Sru_Error403';
				}
			case 'user/penalties':
				if ($acl->sru('user', 'logout')) {
					return 'Sru_UserPenalties';
				} else {
					return 'Sru_Error403';
				}
			default:
				return 'Sru_Error404';
		}
	}
}
