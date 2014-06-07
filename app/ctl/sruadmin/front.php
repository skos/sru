<?
/**
 * front controller czesci administracyjnej sru
 */
class UFctl_SruAdmin_Front
extends UFctl {

	protected function parseParameters() {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$acl = $this->_srv->get('acl');

		try {
			$admin = UFra::factory('UFbean_SruAdmin_Admin');
			$admin->getFromSession();

			if ((is_null($admin->lastPswChange) == true || time() - $admin->lastPswChange > UFra::shared('UFconf_Sru')->passwordValidTime)) {
				$get->view = 'adminOwnPswEdit';
				$get->adminId = $admin->id;
				return;
			}
		} catch (UFex_Core_DataNotFound $ex) {
		}

		$segCount = $req->segmentsCount();
		if (0 == $segCount) {
			$get->view = 'main';
		} else {
			switch ($req->segment(1)) {
				case 'users':
					$ctl = UFra::factory('UFctl_SruAdmin_Users');
					$ctl->go();
					return false;
				case 'computers':
					$ctl = UFra::factory('UFctl_SruAdmin_Computers');
					$ctl->go();
					return false;
				case 'admins':
					$ctl = UFra::factory('UFctl_SruAdmin_Admins');
					$ctl->go();
					return false;
				case 'dormitories':
					$ctl = UFra::factory('UFctl_SruAdmin_Places');
					$ctl->go();
					return false;
				case 'penalties':
					$ctl = UFra::factory('UFctl_SruAdmin_Penalties');
					$ctl->go();
					return false;
				case 'ips':
					$ctl = UFra::factory('UFctl_SruAdmin_Ips');
					$ctl->go();
					return false;
				case 'stats':
					$ctl = UFra::factory('UFctl_SruAdmin_Stats');
					$ctl->go();
					return false;
				case 'switches':
					$ctl = UFra::factory('UFctl_SruAdmin_Switches');
					$ctl->go();
					return false;
				case 'apis':
					$ctl = UFra::factory('UFctl_SruAdmin_Apis');
					$ctl->go();
					return false;
				case 'devices':
					$ctl = UFra::factory('UFctl_SruAdmin_Devices');
					$ctl->go();
					return false;
				case 'inventory':
					$ctl = UFra::factory('UFctl_SruAdmin_Inventory');
					$ctl->go();
					return false;
				default:
					$get->view = 'error404';
					break;
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
		} elseif ('adminOwnPswEdit' == $get->view && $post->is('adminOwnPswEdit') && $acl->sruAdmin('admin', 'edit', $get->adminId)) {
			$act = 'Admin_OwnPswEdit';
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

		switch ($get->view) {
			case 'main':
				if ($acl->sruAdmin('admin', 'logout')) {
					return 'SruAdmin_Main';
				} else {
					return 'SruAdmin_Login';
				}
			case 'adminOwnPswEdit':
				return 'SruAdmin_AdminOwnPswEdit';
			default:
				return 'Sru_Error404';
		}
	}
}
