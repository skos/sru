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
			$alias = $req->segment(2);  
			
			$get->dormAlias = $alias;
			
			
			if($segCount > 2)
			{
				$alias = $req->segment(3);  
				
				$get->roomAlias = $alias;					

				if(3 == $segCount)
				{
					$get->view = 'dormitories/room';
				}
				elseif(4 == $segCount && $req->segment(4) == ':edit' )
				{			
					$get->view = 'dormitories/room/edit';
				}
				else
				{
					$get->view = 'error404';
				}		
			}
			elseif(2 == $segCount)
			{
				if ($req->segment(2) == 'ips') {
					$get->view = 'dormitories/ips';
				} else {
					$get->view = 'dormitories/dorm';
				}
			}
			else
			{
				$get->view = 'error404';
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
		} elseif ('dormitories/room/edit' == $get->view && $post->is('roomEdit')) {
			$act = 'Room_Edit';		
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
			case 'dormitories/main':
				return 'SruAdmin_Dorms';
			case 'dormitories/dorm':
				return 'SruAdmin_Dorm';
			case 'dormitories/ips':
				return 'SruAdmin_Ips';
			case 'dormitories/room':
				return 'SruAdmin_Room';	
			case 'dormitories/room/edit':
				if ($msg->get('roomEdit/ok')) { 
					return 'SruAdmin_Room';
				} else{
					return 'SruAdmin_RoomEdit';
				}								
			default:
				return 'Sru_Error404';
		}
	}
}
