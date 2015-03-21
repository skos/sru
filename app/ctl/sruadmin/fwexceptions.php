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
				default:
					$get->view = 'error404';
					break;
			}
		}
	}
	protected function chooseAction($action = null) {
		$req = $this->_srv->get('req');
		$post = $req->post;
		$acl = $this->_srv->get('acl');

		if ($post->is('adminLogout') && $acl->sruAdmin('admin', 'logout')) {
			$act = 'Admin_Logout';
		} elseif ($post->is('adminLogin') && $acl->sruAdmin('admin', 'login')) {
			$act = 'Admin_Login';
		}
		
		if (isset($act)) {
			$action = 'SruAdmin_'.$act;
		}
		return $action;
	}

	protected function chooseView($view = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$acl = $this->_srv->get('acl');

		if (!$acl->sruAdmin('admin', 'logout')) {
			return 'SruAdmin_Login';
		}
		
		switch ($get->view) {
			case 'fwexceptions/list':
				return 'SruAdmin_FwExceptionList';
			default:
				return 'Sru_Error404';
		}
	}
}
