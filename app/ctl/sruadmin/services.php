<?php
/**
 * front controller czesci administracyjnej sru dotyczacej usług
 */
class UFctl_SruAdmin_Services
extends UFctl {

	protected function parseParameters() 
	{
		$req = $this->_srv->get('req');
		$get = $req->get;
		$acl = $this->_srv->get('acl');
		
		$segCount = $req->segmentsCount();

		// wyzsze segmenty sprawdzane sa w front'ie
		if (1 == $segCount) {
			$get->view = 'services/main';
		} else {
			switch ($req->segment(2)) {
				case 'list':
					$get->view = 'services/list';
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
		} elseif (('services/main' == $get->view || 'services/list' == $get->view) && $post->is('serviceEdit')) {
			$act = 'Service_Edit';
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
			case 'services/main':
				return 'SruAdmin_Services';
			case 'services/list':
				return 'SruAdmin_ServicesList';
			default:
				return 'Sru_Error404';
		}
	}
}
