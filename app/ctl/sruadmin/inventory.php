<?php
/**
 * front controller czesci administracyjnej sru dotyczacej wyposażenia
 */
class UFctl_SruAdmin_Inventory
extends UFctl {

	protected function parseParameters() {
		$req = $this->_srv->get('req');
		$get = $req->get;
		
		$segCount = $req->segmentsCount();

		// wyzsze segmenty sprawdzane sa w front'ie
		if (1 == $segCount) {
			$get->view = 'inventory/main';
		} else {
			$get->view = 'error404';
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
			case 'inventory/main':
				return 'SruAdmin_Inventory';
			default:
				return 'Sru_Error404';
		}
	}
}
