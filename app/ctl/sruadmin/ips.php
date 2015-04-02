<?php
/**
 * front controller czesci administracyjnej sru dotyczacej IP
 */
class UFctl_SruAdmin_Ips
extends UFctl {

	protected function parseParameters() 
	{
		$req = $this->_srv->get('req');
		$get = $req->get;
		
		$segCount = $req->segmentsCount();

		// wyzsze segmenty sprawdzane sa w front'ie
		if (1 == $segCount)	{
			$get->view = 'ips/main';
		} else {
			switch ($req->segment(2)) {
				case 'ds':
					if ($segCount == 3) {
						$alias = $req->segment(3);  
						$get->dormAlias = $alias;
						$get->view = 'ips/main';
					} else {
						$get->view = 'error404';
					}
					break;
				case 'vlan':
					if ($segCount == 3) {
						$vlanId = $req->segment(3);  
						$get->vlanId = (int)$vlanId;
						$get->view = 'ips/main';
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
		
		switch ($get->view) 
		{
			case 'ips/main':
				return 'SruAdmin_Ips';								
			default:
				return 'Sru_Error404';
		}
	}
}
