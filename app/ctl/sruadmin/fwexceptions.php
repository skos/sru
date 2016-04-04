<?php
/**
 * front controller czesci administracyjnej sru dotyczacej wyjatkow w fw
 */
class UFctl_SruAdmin_FwExceptions
extends UFctl {

	protected function parseParameters() 
	{
		$req = $this->_srv->get('req');
		$get = $req->get;
		
		$segCount = $req->segmentsCount();

		// wyzsze segmenty sprawdzane sa w front'ie
		if (1 == $segCount) {
			$get->view = 'fwexceptions/list';
		} else {
			switch ($req->segment(2)) {
				case 'application':
					if ($segCount > 2) {
						$get->appId = (int)$req->segment(3);
						$get->view = 'fwexceptions/edit';
					} else {
						$get->view = 'error404';
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

		if ($post->is('adminLogout') && $acl->sruAdmin('admin', 'logout')) {
			$act = 'Admin_Logout';
		} elseif ($post->is('adminLogin') && $acl->sruAdmin('admin', 'login')) {
			$act = 'Admin_Login';
		} elseif ('fwexceptions/edit' == $get->view && $post->is('fwExceptionApplicationEdit') && $acl->sruAdmin('fwexceptionapplication', 'edit', $get->appId)) {
			$act = 'FwExceptionApplication_Edit';
		}
		
		if (isset($act)) {
			$action = 'SruAdmin_'.$act;
		}
		return $action;
	}

	protected function chooseView($view = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$msg = $this->_srv->get('msg');
		$acl = $this->_srv->get('acl');

		if (!$acl->sruAdmin('admin', 'logout')) {
			return 'SruAdmin_Login';
		}
		
		switch ($get->view) {
			case 'fwexceptions/list':
				return 'SruAdmin_FwExceptionList';
			case 'fwexceptions/edit':
				if ($msg->get('fwExceptionApplicationEdit/ok')) {
					return 'SruAdmin_FwExceptionList';
				} else {
					return 'SruAdmin_FwExceptionEdit';
				}
			default:
				return 'Sru_Error404';
		}
	}
}
