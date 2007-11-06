<?
/**
 * front controller czesci administracyjnej sru dotyczacej komputerow
 */
class UFctl_SruAdmin_Computers
extends UFctl {

	protected function parseParameters() {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$acl = $this->_srv->get('acl');
		
		$segCount = $req->segmentsCount();

		// wyzsze segmenty sprawdzane sa w front'ie
		if (1 == $segCount) {
			$get->view = 'computers/main';
		} else {
			switch ($req->segment(2)) {
				/*
				case 'users':
					$ctl = UFra::factory('UFctl_SruAdmin_Users');
					$req->forward();
					$ctl->go();
					return false;
				case 'computers':
					$ctl = UFra::factory('UFctl_SruAdmin_Computers');
					$req->forward();
					$ctl->go();
					return false;
				*/
				default:
					$get->view = 'computers/computer';
					$id = (int)$req->segment(2);
					if ($id <= 0) {
						$get->view = 'error404';
						break;
					}
					$get->computerId = $id;
					if ($segCount > 2) {
						switch ($req->segment(3)) {
							case 'history':
								$get->view = 'computers/computer/history';
								break;
							case ':edit':
								$get->view = 'computers/computer/edit';
								if ($segCount > 3) {
									$ver = (int)$req->segment(4);
									if ($ver > 0) {
										$get->view = 'computers/computer/restore';
										$get->computerHistoryId = $ver;
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
		} elseif ('computers/computer/edit' == $get->view && $post->is('computerEdit') && $acl->sruAdmin('computer', 'edit')) {
			$act = 'Computer_Edit';
		} elseif ('computers/computer/edit' == $get->view && $post->is('computerDel') && $acl->sruAdmin('computer', 'del')) {
			$act = 'Computer_Del';
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
			case 'computers/main':
				return 'SruAdmin_Main';
			case 'computers/computer':
				return 'SruAdmin_Computer';
			case 'computers/computer/history':
				return 'SruAdmin_ComputerHistory';
			case 'computers/computer/edit':
				if ($msg->get('computerDel/ok')) {
					return 'SruAdmin_Main';
				} else {
					return 'SruAdmin_ComputerEdit';
				}
			case 'computers/computer/restore':
				return 'SruAdmin_ComputerRestore';
			default:
				return 'Sru_Error404';
		}
	}
}
