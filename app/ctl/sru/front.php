<?
/**
 * front controller modulu sru
 */
class UFctl_Sru_Front
extends UFctl {

	protected function parseParameters() {
		$req = $this->_srv->get('req');
		$get = $req->get;
		
		$segCount = $req->segmentsCount();
		if (0 == $segCount) {
			$get->view = 'login';
		} else {
			switch ($req->segment(1)) {
				case 'create':
					$get->view = 'user/add';
					break;
				default:
					$get->view = 'show';
					break;
			}
		}
	}

	protected function chooseAction($action = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$post = $req->post;
		$acl = $this->_srv->get('acl');

		if ('user/add' == $get->view && $post->is('userAdd') && $acl->sru('user', 'add')) {
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
			case 'user/add':
				return 'Sru_UserAdd';
			default:
				return 'Sru_Error404';
		}
	}
}
