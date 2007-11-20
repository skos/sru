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
		} elseif ('admins/edit' == $get->view && $post->is('adminEdit') && $acl->sruAdmin('admin', 'edit', $get->adminId)) {
			$act = 'Admin_Edit';
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
				} elseif ($acl->sruAdmin('admin', 'add')) {
					return 'SruAdmin_AdminAdd';
				} else {
					return 'Sru_Error404';
				}
			case 'admins/edit':
			/*	if ($msg->get('adminEdit/ok')) { //@todo lepiej zrobic chyab redirectai tak zeby byla ramka
[17:25:18] hrynek: te ramke, to sie reczenie robi dopisujac do ktoregos tpl-a od boksa
[17:25:34] hrynek: w sumie mozna i przekierowywac, ale wtedy tez trzeba pokazac jakis komunikat
[17:25:46] hrynek: popatrz na UFra::redirect()
[17:26:44] hrynek: i $_srv->get('msgNext')
[17:27:12] hrynek: to jest identyczne, jak 'msg', ale dane przejda w nastepnym requescie do 'msg'
[17:28:00] hrynek: ale tak czy siak musisz gdzies pokazac ramke na podstawie zawartosci 'msg'
					return 'SruAdmin_Admin';
				}else*/
				if ($acl->sruAdmin('admin', 'edit', $get->adminId)) {
					return 'SruAdmin_AdminEdit';
				} else {
					return 'Sru_Error403';
				}											
			default:
				return 'Sru_Error404';
		}
	}
}
