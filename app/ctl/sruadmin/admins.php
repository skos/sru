<?php
/**
 * front controller czesci administracyjnej sru dotyczacej administratorow
 */
class UFctl_SruAdmin_Admins
extends UFctl {

	protected function parseParameters() {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$acl = $this->_srv->get('acl');
		
		$segCount = $req->segmentsCount();

		// wyzsze segmenty sprawdzane sa w front'ie
		if (1 == $segCount)	
		{
			$get->view = 'admins/main';
		} 
		else
		{
			switch ($req->segment(2)) 
			{
				case ':add':
					$get->view = 'admins/add';
					break;
				case 'active':
					$get->view = 'admins/active';
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
								$get->view = 'admins/admin/edit';
								break;
							case 'history':
								$get->view = 'admins/admin/history';
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
		} elseif ('admins/add' == $get->view && $post->is('adminAdd') && $acl->sruAdmin('admin', 'add')) {
			$act = 'Admin_Add';
		} elseif ('admins/admin/edit' == $get->view && $post->is('adminEdit') && $acl->sruAdmin('admin', 'edit', $get->adminId)) {
			$act = 'Admin_Edit';
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
			case 'admins/main':
				return 'SruAdmin_Admins';
			case 'admins/admin':
				return 'SruAdmin_Admin';
			case 'admins/admin/history':
				return 'SruAdmin_AdminHistory';
			case 'admins/add':
				if ($msg->get('adminAdd/ok')) {
					return 'SruAdmin_Admins';
				} elseif ($acl->sruAdmin('admin', 'add')) {
					return 'SruAdmin_AdminAdd';
				} else {
					return 'Sru_Error404';
				}
			case 'admins/admin/edit':
				if ($msg->get('adminEdit/ok')) { 
					return 'SruAdmin_Admin';
				} elseif ($acl->sruAdmin('admin', 'edit', $get->adminId)) {
					return 'SruAdmin_AdminEdit';
				} else {
					return 'Sru_Error403';
				}
			case 'admins/active':
				try {
					$get->activeOn = $post->activeOnForm['activeOn'];
				} catch (UFex_Core_DataNotFound $ex) {
					$get->activeOn = 0;
				}
				return 'SruAdmin_AdminsActive';
			default:
				return 'Sru_Error404';
		}
	}
}
