<?
/**
 * front controller modulu Walet
 */
class UFctl_SruWalet_Front
extends UFctl {

	protected function parseParameters() {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$acl = $this->_srv->get('acl');

		$segCount = $req->segmentsCount();
		if (0 == $segCount) {
			$get->view = 'main';
		} else {
			switch ($req->segment(1)) {
				case 'users':
					if (1 == $segCount) {
						$get->view = 'users/main';
					} else {
						switch ($req->segment(2)) {
							case 'search':
								$get->view = 'users/search';
								for ($i = 3; $i <= $segCount; ++$i) {
									$tmp = explode(':', $req->segment($i), 2);
									switch ($tmp[0]) {
										case 'surname':
											$get->searchedSurname = urldecode($tmp[1]);
											break;
									}
								}
								break;
							case ':add':
								$get->view = 'users/user/add';
								break;
							default:
								$get->view = 'users/user';
								$id = (int)$req->segment(2);
								if ($id <= 0) {
									$get->view = 'error404';
									break;
								}
								$get->userId = $id;
								if ($segCount > 2) {
									switch ($req->segment(3)) {
										case 'history':
											$get->view = 'users/user/history';
											break;
										case ':edit':
											$get->view = 'users/user/edit';
											break;
										default:
											$get->view = 'error404';
											break;
									}
								}
								break;
						}
					}
					break;
				case 'inhabitants':
					$get->view = 'inhabitants/main';
					break;
				case 'dormitories':
					if (1 == $segCount) {
						$get->view = 'dormitories/main';
					} else {
						$alias = $req->segment(2);  
						$get->dormAlias = $alias;
						if($segCount == 2) {
							$get->view = 'dormitories/dorm';
						} else {
							$get->view = 'error404';
						}
					}
					break;
				case 'stats':
					if (1 == $segCount) {
						$get->view = 'stats/users';
						break;
					} else {
						switch ($req->segment(2)) {
							case 'dormitories':
								$get->view = 'stats/dormitories';
								break;
							default:
								$get->view = 'error404';
								break;
						}
					}
					break;
				case 'admins':
					if (1 == $segCount) {
						$get->view = 'admins/main';
						break;
					} else {
						switch ($req->segment(2)) {
							case ':add':
								$get->view = 'admins/add';
								break;
							default:
								$get->view = 'admins/admin';
								$id = (int)$req->segment(2);
								if ($id <= 0) {
									$get->view = 'error404';
									break;
								}
								$get->adminId = $id;

								if ($segCount > 2) {
									switch ($req->segment(3)) {
										case ':edit':
											$get->view = 'admins/edit';
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

	protected function chooseAction($action = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$post = $req->post;
		$acl = $this->_srv->get('acl');

		if ($post->is('adminLogout') && $acl->sruWalet('admin', 'logout')) {
			$act = 'Admin_Logout';
		} elseif ($post->is('adminLogin') && $acl->sruWalet('admin', 'login')) {
			$act = 'Admin_Login';
		} elseif ($post->is('userSearch')) {
			$act = 'User_Search';
		} elseif ('users/user/add' == $get->view && $post->is('userAdd') && $acl->sruWalet('user', 'add')) {
			$act = 'User_Add';
		} elseif ($post->is('userEdit') && $acl->sruWalet('user', 'edit')) {
			$act = 'User_Edit';
		} elseif ('users/user/edit' == $get->view && $post->is('userDel') && $acl->sruWalet('user', 'del')) {
			$act = 'User_Del';
		} elseif ('admins/add' == $get->view && $post->is('adminAdd') && $acl->sruWalet('admin', 'add')) {
			$act = 'Admin_Add';
		} elseif ('admins/edit' == $get->view && $post->is('adminEdit') && $acl->sruWalet('admin', 'edit', $get->adminId)) {
			$act = 'Admin_Edit';
		}

		if (isset($act)) {
			$action = 'SruWalet_'.$act;
		}

		return $action;
	}

	protected function chooseView($view = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$post= $req->post;
		$msg = $this->_srv->get('msg');
		$acl = $this->_srv->get('acl');

		if (!$acl->sruWalet('admin', 'logout')) {
			return 'SruWalet_Login';
		}
		switch ($get->view) {
			case 'main':
				if ($acl->sruWalet('admin', 'logout')) {
					return 'SruWalet_Main';
				} else {
					return 'SruWalet_Login';
				}
			case 'users/main':
				return 'SruWalet_Main';
			case 'users/search':
				return 'SruWalet_UserSearchResults';
			case 'users/user':
				return 'SruWalet_User';
			case 'users/user/history':
				return 'SruWalet_UserHistory';
			case 'users/user/servicehistory':
				return 'SruWalet_ServiceHistory';
			case 'users/user/edit':
				if ($msg->get('userDel/ok')) {
					return 'SruWalet_Main';
				} else {
					return 'SruWalet_UserEdit';
				}
			case 'users/user/add':
				if ($msg->get('userAdd/ok')) {
					return 'SruWalet_User';
				} else {
					return 'SruWalet_UserAdd';
				} 
			case 'inhabitants/main':
				return 'SruWalet_Inhabitants';
			case 'dormitories/main':
				return 'SruWalet_Dormitories';
			case 'dormitories/dorm':
				if ($acl->sruWalet('dorm', 'view', $get->dormAlias)) {
					return 'SruWalet_Dorm';
				} else {
					return 'Sru_Error403';
				}
			case 'stats/users':
				return 'SruWalet_StatsUsers';
			case 'stats/dormitories':
				return 'SruWalet_StatsDormitories';
			case 'admins/main':
				return 'SruWalet_Admins';
			case 'admins/admin':
				return 'SruWalet_Admin';
			case 'admins/add':
				if ($msg->get('adminAdd/ok')) {
					return 'SruWalet_Admins';
				} elseif ($acl->sruWalet('admin', 'add')) {
					return 'SruWalet_AdminAdd';
				} else {
					return 'Sru_Error404';
				}
			case 'admins/edit':
				if ($msg->get('adminEdit/ok')) { 
					return 'SruWalet_Admin';
				} elseif ($acl->sruWalet('admin', 'edit', $get->adminId)) {
					return 'SruWalet_AdminEdit';
				} else {
					return 'Sru_Error403';
				}
			default:
				return 'Sru_Error404';
		}
	}
}
