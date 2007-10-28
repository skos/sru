<?
/**
 * front controller modulu sru
 */
class UFctl_Sru_Front
extends UFctl {

	protected function parseParameters() {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$acl = $this->_srv->get('acl');
		
		$segCount = $req->segmentsCount();
		if (0 == $segCount) {
			if ($acl->sru('user', 'edit')) {
				$get->view = 'user/main';
			} else {
				$get->view = 'login';
			}
		} elseif ($acl->sru('user', 'edit')) {	// zalogowani
			switch ($req->segment(1)) {
				case 'profile':
					$get->view = 'user/edit';
					break;
				default:
					$get->view = 'user/main';
					break;
			}
		} else {	// anonimowi
			switch ($req->segment(1)) {
				case 'create':
					$get->view = 'user/add';
					break;
				default:
					$get->view = 'login';
					break;
			}
		}
	}

	protected function chooseAction($action = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$post = $req->post;
		$acl = $this->_srv->get('acl');

		if ($post->is('userLogout') && $acl->sru('user', 'logout')) {
			$act = 'User_Logout';
		} elseif ('login' == $get->view && $post->is('userLogin') && $acl->sru('user', 'login')) {
			$act = 'User_Login';
		} elseif ('user/edit' == $get->view && $post->is('userEdit') && $acl->sru('user', 'edit')) {
			$act = 'User_Edit';
		} elseif ('user/add' == $get->view && $post->is('userAdd') && $acl->sru('user', 'add')) {
			$act = 'User_Add';
		}

		if (isset($act)) {
			$action = 'Sru_'.$act;
		}

		return $action;
	}

	protected function chooseView($view = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$post= $req->post;
		$msg = $this->_srv->get('msg');
		$acl = $this->_srv->get('acl');

		switch ($get->view) {
			case 'login':
				return 'Sru_Login';
			case 'user/main':
				return 'Sru_UserMain';
			case 'user/add':
				return 'Sru_UserAdd';
			case 'user/edit':
				return 'Sru_UserEdit';
			default:
				return 'Sru_Error404';
		}
	}
}
