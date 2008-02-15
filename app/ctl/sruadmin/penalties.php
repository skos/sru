<?php
/**
 * front controller czesci administracyjnej sru dotyczacej kar
 */
class UFctl_SruAdmin_Penalties
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
			$get->view = 'penalties/main';
		} 
		else
		{
			switch ($req->segment(2)) 
			{			
				case ':add':
					$id = (int)$req->segment(3);
					if ($id <= 0) {
							$get->view = 'error404';
				
					}	else {		
					$get->userId = $id;								
					$get->view = 'penalties/add';
					}
					break;
				default:
					$get->view = 'penalties/penalty';
					$id = (int)$req->segment(2);
					if ($id <= 0) {
						$get->view = 'error404';
						break;
					}
					$get->penaltyId = $id;

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
		} elseif ('penalties/add' == $get->view && $post->is('penaltyAdd') && $acl->sruAdmin('penalty', 'add')) {
			$act = 'Penalty_Add';
		}/* elseif ('admins/edit' == $get->view && $post->is('adminEdit') && $acl->sruAdmin('admin', 'edit', $get->adminId)) {
			$act = 'Admin_Edit';
		}
*/
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
			case 'penalties/main':
				return 'SruAdmin_Penalties';
			case 'penalties/penalty':
				return 'SruAdmin_Penalty';
			case 'penalties/add':
				if ($msg->get('penaltyAdd/ok')) {
					return 'SruAdmin_Penalties';
				} elseif ($acl->sruAdmin('penalty', 'add')) {
					return 'SruAdmin_PenaltyAdd';
				} else {
					return 'Sru_Error404';
				}											
			default:
				return 'Sru_Error404';
		}
	}
}
