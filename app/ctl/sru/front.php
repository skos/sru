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
			if ($acl->sru('user', 'edit')) {
				$get->view = 'user/main';
			} else {
				$get->view = 'login';
			}
		} elseif ($acl->sru('user', 'edit')) {	// zalogowani
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
								} elseif ($segCount > 2 && ':del' == $req->segment(3)) {
									$get->view = 'user/computer/del';
								} else {
									$get->view = 'user/computer';
								}
						}
					} else {
						$get->view = 'user/computers';
					}
					break;
				default:
					$get->view = 'user/main';
					break;
			}
		} else {	// anonimowi
			switch ($req->segment(1)) {
				case 'create':
					$get->view = 'user/add';
					break;
				default:
					$get->view = 'login';
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
		} elseif ('login' == $get->view && $post->is('userLogin') && $acl->sru('user', 'login')) {
			$act = 'User_Login';
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
			case 'login':
				return 'Sru_Login';
			case 'user/main':
				return 'Sru_UserMain';
			case 'user/add':
				return 'Sru_UserAdd';
			case 'user/edit':
				return 'Sru_UserEdit';
			case 'user/computers':
				return 'Sru_UserComputers';
			case 'user/computer':
				return 'Sru_UserComputer';
			case 'user/computer/add':
				if ($msg->get('computerAdd/ok')) {
					return 'Sru_UserComputers';	
				} else {
					return 'Sru_UserComputerAdd';
				}
			case 'user/computer/edit':
				if ($msg->get('computerDel/ok')) {
					return 'Sru_UserComputers';	
				} else {
					return 'Sru_UserComputerEdit';
				}
			case 'user/computer/del':
				return 'Sru_UserComputerDel';
			default:
				return 'Sru_Error404';
		}
	}
}
