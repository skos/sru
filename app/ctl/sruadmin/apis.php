<?php
/**
 * front controller czesci administracyjnej sru dotyczacej zapytań do zew. API
 */
class UFctl_SruAdmin_Apis
extends UFctl {

	protected function parseParameters() 
	{
		$req = $this->_srv->get('req');
		$get = $req->get;
		
		$segCount = $req->segmentsCount();

		// wyzsze segmenty sprawdzane sa w front'ie
		if (1 == $segCount)	{
			$get->view = 'error404';
		} else {
			switch ($req->segment(2)) {
				case 'otrstickets':
					if ($segCount > 2) {
						$get->view = 'error404';
					} else {  
						$get->view = 'otrs/tickets';
					}
					break;
				case 'zabbixproblems':
					if ($segCount > 2) {
						$get->view = 'error404';
					} else {  
						$get->view = 'zabbix/problems';
					}
					break;
				case 'getmacvendor':
					if ($segCount > 2) {
						$get->mac = urldecode($req->segment(3));
						$get->view = 'macVendors/getVendor';
					} else {
						$get->view = 'error404';
					}
					break;
				case 'gettaskssummary':
					$get->view = 'tasks/getSummary';
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
			case 'otrs/tickets':
				return 'SruAdmin_ApisOtrsTickets';
			case 'zabbix/problems':
				return 'SruAdmin_ApisZabbixProblems';
			case 'macVendors/getVendor':
				return 'SruAdmin_ApisGetMacVendor';
			case 'tasks/getSummary':
				return 'SruAdmin_ApisGetTasksSummary';
			default:
				return 'Sru_Error404';
		}
	}
}
