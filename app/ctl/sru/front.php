<?
/**
 * front controller modulu sru
 */
class UFctl_Sru_Front
extends UFctl {

	protected function parseParameters() {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$segCount = $req->segmentsCount();

		if ($segCount < 1 || $req->segment(1) != 'logout') {
			try {
				$user = UFra::factory('UFbean_Sru_User');
				$user->getFromSession();

				if ($user->updateNeeded || $user->changePasswordNeeded) {
					$get->view = 'user/edit';
					return;
				}
			} catch (UFex_Core_DataNotFound $e) {
			}
		}

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
								} else if ($segCount > 2 && ':activate' == $req->segment(3)) {
									$get->view = 'user/computer/activate';
								} else if ($segCount > 2 && ':fwexceptionsadd' == $req->segment(3)) {
									$get->view = 'user/computer/fwexceptionsadd';
								break;
								} else {
									$get->view = 'user/computer';
								}
						}
					} else {
						$get->view = 'user/computers';
					}
					break;
				case 'penalties':
					$get->view = 'user/penalties';
					break;
				case 'applications':
					if ($segCount > 1) {
						switch ($req->segment(2)) {
							case 'fwexceptions':
								if ($segCount > 2) {
									$get->appId = (int)$req->segment(3);
									$get->view = 'applications/fwexceptions/edit';
								} else {
									$get->view = 'applications/fwexceptions/list';
								}
								break;
							default:
								$get->view = 'error404';
								break;
						}
					} else {
						$get->view = 'error404';
					}
					break;
				case 'unregistered':
					$get->view = 'user/unregistered';
					break;
				case 'banned':
					$get->view = 'user/banned';
					break;
				case 'logout':
					$ctl = UFra::factory('UFact_Sru_User_Logout');
					$ctl->go();
					UFlib_Http::redirect(UFURL_BASE.'/'.implode('/', $this->_srv->get('req')->segments(0)));
					return false;
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
		$sess = $this->_srv->get('session');

		if ($post->is('userLogout') && $acl->sru('user', 'logout')) {
			$act = 'User_Logout';
		} elseif ($post->is('userLogin') && $acl->sru('user', 'login')) {
			$act = 'User_Login';
		} elseif ('user/main' == $get->view && $post->is('sendPassword') && $acl->sru('user', 'login')) {
			$act = 'User_SendPassword';
		} elseif ('user/main' == $get->view && $get->is('userToken') && $acl->sru('user', 'recover')) { 
			$act = 'User_Recover';  
		} elseif ('user/edit' == $get->view && $post->is('userEdit') && $acl->sru('user', 'edit')) {
			$act = 'User_Edit';
		} elseif ('user/computer/edit' == $get->view && $post->is('computerEdit') && $acl->sru('computer', 'edit')) {
			$act = 'Computer_Edit';
		} elseif ('user/computer/activate' == $get->view && $post->is('computerEdit') && $acl->sru('computer', 'add')) {
			$act = 'Computer_Edit';
		} elseif ('user/computer/fwexceptionsadd' == $get->view && $post->is('computerFwExceptionsAdd') && $acl->sru('computer', 'edit')) {
			$act = 'Computer_FwExceptionsAdd';
		} elseif ('applications/fwexceptions/edit' == $get->view && $post->is('fwExceptionApplicationEdit') && $acl->sru('fwexception', 'editApp', $get->appId)) {
			$act = 'FwExceptionApplication_Edit';
		} elseif ('user/computer/add' == $get->view && $post->is('computerAdd') && $acl->sru('computer', 'add')) {
			$act = 'Computer_Add';
		} elseif ('user/computer/edit' == $get->view && $post->is('computerDel') && $acl->sru('computer', 'del')) {
			$act = 'Computer_Del';
		} elseif ('user/main' == $get->view && $post->is('sendMessage') && $acl->sru('user', 'edit')) {
			$act = 'User_SendMessage';
		}
		
		// jeśli user nie wysyła wiadomości, zerujemy znacznik wysłania (F5 issue, #757)
		if (!isset($act) || (isset($act) && $act != 'User_SendMessage')) {
			$sess->otrsMsgSend = 0;
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
			case 'user/edit':
				if ($msg->get('userEdit/ok')) {
					return 'Sru_UserMain';
				} else if ($acl->sru('user', 'edit')) {
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
				} else if ($msg->get('computerEdit/ok')) {
					return 'Sru_UserComputers';
				} elseif ($acl->sru('computer', 'edit')) {
					return 'Sru_UserComputerEdit';
				} else {
					return 'Sru_Error403';
				}
			case 'user/computer/activate':
				if ($msg->get('computerEdit/ok')) {
					return 'Sru_UserComputers';
				} else if ($acl->sru('computer', 'add')) {
					return 'Sru_UserComputerActivate';
				} else {
					return 'Sru_Error403';
				}
			case 'user/computer/fwexceptionsadd':
				if ($msg->get('computerFwExceptionsAdd/ok')) {
					return 'Sru_UserComputers';
				} else if ($acl->sru('computer', 'edit')) {
					return 'Sru_UserComputerFwExceptionsAdd';
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
			case 'applications/fwexceptions/list':
				if ($acl->sru('fwexception', 'edit')) {
					return 'Sru_ApplicationFwExceptions';
				} else {
					return 'Sru_Error403';
				}
			case 'applications/fwexceptions/edit':
				if ($msg->get('fwExceptionApplicationEdit/ok')) {
					return 'Sru_ApplicationFwExceptions';
				} else if ($acl->sru('fwexception', 'editApp', $get->appId)) {
					return 'Sru_ApplicationFwExceptionEdit';
				} else {
					return 'Sru_Error403';
				}
			case 'user/unregistered':
				if ($acl->sru('user', 'login')) {
					return 'Sru_UserUnregistered';
				}else{
					return 'Sru_UserMain';
				}
			case 'user/banned':
				if ($acl->sru('user', 'login')) {
					return 'Sru_UserBanned';
				}else{
					return 'Sru_UserMain';
				}
			default:
				return 'Sru_Error404';
		}
	}
}
