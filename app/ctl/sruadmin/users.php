<?
/**
 * front controller czesci administracyjnej sru dotyczacej uzytkownikow
 */
class UFctl_SruAdmin_Users
extends UFctl {

	protected function parseParameters() {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$acl = $this->_srv->get('acl');
		
		$segCount = $req->segmentsCount();

		// wyzsze segmenty sprawdzane sa w front'ie
		if (1 == $segCount) {
			$get->view = 'users/main';
		} else {
			switch ($req->segment(2)) {
				case 'search':
					$get->view = 'users/search';
					for ($i=3; $i<=$segCount; ++$i) {
						$tmp = explode(':', $req->segment($i), 2);
						switch ($tmp[0]) {
							case 'name':
								$get->searchedName = urldecode($tmp[1]);
								break;
							case 'login':
								$get->searchedLogin = urldecode($tmp[1]);
								break;
							case 'surname':
								$get->searchedSruname = urldecode($tmp[1]);
								break;
						}
					}
					break;
				default:
					$get->view = 'users/user';
					$id = (int)$req->segment(2);
					if ($id <= 0) {
						$get->view = 'error404';
						break;
					}
					$get->userId = $id;
					if ($segCount > 3) 
					{
						if($req->segment(3) == 'computers' && $req->segment(4) == ':add')
						{
							$get->view = 'users/user/computers/add';
						}
					}
					else if ($segCount > 2) {
						switch ($req->segment(3)) {
							case 'history':
								$get->view = 'users/user/history';
								break;
							case ':edit':
								$get->view = 'users/user/edit';
								if ($segCount > 3) {
									$ver = (int)$req->segment(4);
									if ($ver > 0) {
										$get->view = 'users/user/restore';
										$get->userHistoryId = $ver;
									}
								}
								break;
							default:
								$get->view = 'error404';
								break;
						}
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
		
		if ($post->is('adminLogout') && $acl->sruAdmin('admin', 'logout')) {
			$act = 'Admin_Logout';
		} elseif ($post->is('adminLogin') && $acl->sruAdmin('admin', 'login')) {
			$act = 'Admin_Login';
		} elseif ('users/user/computers/add' == $get->view && $post->is('computerAdd')) {
			$act = 'Computer_Add';			
		} elseif ($post->is('userSearch')) {
			$act = 'User_Search';
		} elseif ('users/user/edit' == $get->view && $post->is('userEdit') && $acl->sruAdmin('user', 'edit')) {
			$act = 'User_Edit';
		} elseif ('users/user/edit' == $get->view && $post->is('userDel') && $acl->sruAdmin('user', 'del')) {
			$act = 'User_Del';
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
		switch ($get->view) {
			case 'users/main':
				return 'SruAdmin_UserSearch';
			case 'users/search':
				return 'SruAdmin_UserSearchResults';
			case 'users/user':
				return 'SruAdmin_User';
			case 'users/user/history':
				return 'SruAdmin_UserHistory';
			case 'users/user/edit':
				if ($msg->get('userDel/ok')) {
					return 'SruAdmin_Main';
				} else {
					return 'SruAdmin_UserEdit';
				}
			case 'users/user/computers/add':
				if ($msg->get('computerAdd/ok')) {
					return 'SruAdmin_User';
				} else {
					return 'SruAdmin_ComputerAdd';
				} 		
			case 'users/user/restore':
				return 'SruAdmin_UserRestore';
			default:
				return 'Sru_Error404';
		}
	}
}
