<?php
/**
 * front controller czesci administracyjnej sru dotyczacej pokoi
 */
class UFctl_SruAdmin_Places
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
			$get->view = 'dormitories/main';
		} 
		else
		{
			$alias = $req->segment(2);  //@todo jak sprawdzac poprawnosc?
			$get->dormAlias = $alias;

			if(2 == $segCount)
			{
				
				$get->view = 'dormitories/dorm';
			}
			else
			{
				$get->view = 'error404';
			}
			/*			
			switch ($req->segment(1)) 
			{	
				case 'dormitories':
					$get->view = 'dormitories/main';
					break;
				default:
					$get->view = 'error404';
					break;
			*/	
		/*			$get->view = 'places/dorm';
					$id = (int)$req->segment(2);
					if ($id <= 0) {
						$get->view = 'error404';
						break;
					}
					$get->adminId = $id;

					if ($segCount > 2) {
						switch ($req->segment(3)) {
							case ':edit':
								$get->view = 'admins/edit';
								break;
							default:
								$get->view = 'error404';
								break;
						}
					}
				}*/	
		}
	}
	protected function chooseAction($action = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$post = $req->post;
		$acl = $this->_srv->get('acl');
		
	/*		
		if ('admins/add' == $get->view && $post->is('adminAdd') && $acl->sruAdmin('admin', 'add')) {
			$act = 'Admin_Add';
		} elseif ('admins/edit' == $get->view && $post->is('adminEdit') && $acl->sruAdmin('admin', 'edit', $get->adminId)) {
			$act = 'Admin_Edit';
		}*/

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
			case 'dormitories/main':
				return 'SruAdmin_Dorms';
			case 'dormitories/dorm':
				return 'SruAdmin_Dorm';
	/*		case 'admins/add':
				if ($msg->get('adminAdd/ok')) {
					return 'SruAdmin_Admins';
				} elseif ($acl->sruAdmin('admin', 'add')) {
					return 'SruAdmin_AdminAdd';
				} else {
					return 'Sru_Error404';
				}
			case 'admins/edit':
				if ($msg->get('adminEdit/ok')) { 
					return 'SruAdmin_Admin';
				} elseif ($acl->sruAdmin('admin', 'edit', $get->adminId)) {
					return 'SruAdmin_AdminEdit';
				} else {
					return 'Sru_Error403';
				}		*/									
			default:
				return 'Sru_Error404';
		}
	}
}
