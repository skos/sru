<?php
/**
 * front controller czesci administracyjnej sru dotyczacej kar
 */
class UFctl_SruAdmin_Penalties
extends UFctl_Common {

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
					$found = false;
					for ($i=3; $i<=$segCount; ++$i) {
						if (!$found && $this->isParamInt($i, 'computer')) {
							$tmp = (int)$this->fetchParam($i, 'computer');
							if ($tmp > 0) {
								try {
									$bean = UFra::factory('UFbean_Sru_Computer');
									$bean->getByPK($tmp);
									$get->userId = $bean->userId;
									$get->computerId = $tmp;
									$found = true;
								} catch (UFex_Dao_NotFound $e) {
								}
							}
						} elseif (!$found && $this->isParam($i, 'ip:[0-9]{1,3}(\.[0-9]{1,3}){3}')) {
							$tmp = $this->fetchParam($i, 'ip');
							try {
								$bean = UFra::factory('UFbean_Sru_Computer');
								$bean->getByIp($tmp);
								$get->computerId = $bean->id;
								$get->userId = $bean->userId;
								$found = true;
							} catch (UFex_Dao_NotFound $e) {
							}
						} elseif (!$found && $this->isParamInt($i, 'user')) {
							$tmp = (int)$this->fetchParam($i, 'user');
							if ($tmp > 0) {
								$get->userId = $tmp;
								$found = true;
							}
						} elseif ($this->isParamInt($i, 'template')) {
							$tmp = (int)$this->fetchParam($i, 'template');
							$get->templateId = $tmp;
						}
					}

					$get->view = 'penalties/add';
					break;
				case 'actions':
					$get->view = 'penalties/actions';
					break;
				case 'history':
					$get->view = 'penalties/history';
					break;
				default:
					$get->view = 'penalties/penalty';
					$id = (int)$req->segment(2);
					if ($id <= 0) {
						$get->view = 'error404';
						break;
					}
					$get->penaltyId = $id;
					if ($segCount > 2) {
						switch ($req->segment(3)) {
							case 'history':
								$get->view = 'penalties/penalty/history';
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
		
		if ($post->is('adminLogout') && $acl->sruAdmin('admin', 'logout')) {
			$act = 'Admin_Logout';
		} elseif ($post->is('adminLogin') && $acl->sruAdmin('admin', 'login')) {
			$act = 'Admin_Login';
		} elseif (('penalties/penalty' == $get->view || 'penalties/penalty/history' == $get->view) && $post->is('penaltyEdit') && $acl->sruAdmin('penalty', 'edit')) {
			$act = 'Penalty_Edit';
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
					if (!$get->is('userId')) {	
						return 'Sru_Error404';
					} elseif ($get->is('templateId')) {
						return 'SruAdmin_PenaltyAdd';
					} else {
						return 'SruAdmin_PenaltyTemplateChoose';
					}
				} else {
					return 'Sru_Error404';
				}	
			case 'penalties/actions':
				return 'SruAdmin_PenaltyActions';
			case 'penalties/penalty/history':
				return 'SruAdmin_PenaltyHistory';
			default:
				return 'Sru_Error404';
		}
	}
}
