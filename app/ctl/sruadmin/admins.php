<?php
/**
 * front controller czesci administracyjnej sru dotyczacej administratorow
 */
class UFctl_SruAdmin_Admins
extends UFctl {

	protected function parseParameters() 
	{
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
				default:
					$get->view = 'admins/admin';
					$id = (int)$req->segment(2);
					if ($id <= 0)
					{
						$get->view = 'error404';
						break;
					}
					$get->adminId = $id;
					break;
				}
		}
	}
	protected function chooseAction($action = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$post = $req->post;
		$acl = $this->_srv->get('acl');
		
		if ('admins/add' == $get->view && $post->is('adminAdd') && $acl->sruAdmin('admin', 'add')) {
			$act = 'Admin_Add';
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
			case 'admins/add':
				if ($msg->get('adminAdd/ok')) {
					return 'SruAdmin_Admins';
				} elseif ($acl->sruAdmin('admin', 'add')) { //@todo jakies uprawnienia, idtype w sesji?
					return 'SruAdmin_AdminAdd';
				} else {
					return 'Sru_Error404';
				}								
			default:
				return 'Sru_Error404';
		}
	}
}
