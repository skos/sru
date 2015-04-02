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
		if (1 == $segCount) {
			$get->view = 'penalties/main';
		} else {
			switch ($req->segment(2)) {
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
				case 'active':
					$get->view = 'penalties/active';
					break;
				case 'templates':
					if ($segCount == 2) {
						$get->view = 'penalties/templates';
					} else {
						switch ($req->segment(3)) {
							case ':add':
								$get->view = 'penalties/template/add';
								break;
							default:
								$id = (int)$req->segment(3);
								if ($id <= 0) {
									$get->view = 'error404';
									break;
								}
								$get->penaltyTemplateId = $id;
								if ($segCount > 3) {
									switch ($req->segment(4)) {
										case ':edit':
											$get->view = 'penalties/template/edit';
											break;
									}
								}
						}
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
					if ($segCount > 2) {
						switch ($req->segment(3)) {
							case ':edit':
								if ($segCount > 3) {
									switch ($req->segment(4)) {
										case 'changeTemplate':
											if ($segCount == 5 && $this->isParamInt(5, 'template')) {
												$tmp = (int)$this->fetchParam(5, 'template');
												$get->templateId = $tmp;
												$get->view = 'penalties/penalty/edit';
											} else {
												$get->view = 'penalties/penalty/editTemplate';
											}
											break;
										default:
											$get->view = 'error404';
											break;
									}
								} else {
									$get->view = 'penalties/penalty/edit';
									break;
								}
								break;
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
		} elseif ('penalties/penalty/edit' == $get->view && $post->is('penaltyEdit') && $acl->sruAdmin('penalty', 'edit')) {
			$act = 'Penalty_Edit';
		} elseif ('penalties/add' == $get->view && $post->is('penaltyAdd') && $acl->sruAdmin('penalty', 'add')) {
			$act = 'Penalty_Add';
		} elseif ('penalties/template/add' == $get->view && $post->is('penaltyTemplateAdd') && $acl->sruAdmin('penaltyTemplate', 'add')) {
			$act = 'PenaltyTemplate_Add';
		} elseif ('penalties/template/edit' == $get->view && $post->is('penaltyTemplateEdit') && $acl->sruAdmin('penaltyTemplate', 'edit')) {
			$act = 'PenaltyTemplate_Edit';
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
			case 'penalties/main':
				return 'SruAdmin_PenaltyActions';
			case 'penalties/penalty':
				return 'SruAdmin_Penalty';
			case 'penalties/add':
				if ($msg->get('penaltyAdd/ok')) {
					return 'SruAdmin_Penalty';
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
			case 'penalties/active':
				return 'SruAdmin_Penalties';
			case 'penalties/templates':
				return 'SruAdmin_PenaltyTemplates';
			case 'penalties/template/add':
				if ($msg->get('penaltyTemplateAdd/ok')) { 
					return 'SruAdmin_PenaltyTemplates';
				} elseif ($acl->sruAdmin('penaltyTemplate', 'add')) {
					return 'SruAdmin_PenaltyTemplateAdd';
				} else {
					return 'Sru_Error403';
				}
			case 'penalties/template/edit':
				if ($msg->get('penaltyTemplateEdit/ok')) { 
					return 'SruAdmin_PenaltyTemplates';
				} elseif ($acl->sruAdmin('penaltyTemplate', 'edit')) {
					return 'SruAdmin_PenaltyTemplateEdit';
				} else {
					return 'Sru_Error403';
				}
			case 'penalties/penalty/edit':
				if ($msg->get('penaltyEdit/ok')) { 
					return 'SruAdmin_Penalty';
				} elseif ($acl->sruAdmin('penalty', 'editOne', $get->penaltyId) || $acl->sruAdmin('penalty', 'editOnePartly', $get->penaltyId)) {
					return 'SruAdmin_PenaltyEdit';
				} else {
					return 'Sru_Error403';
				}
			case 'penalties/penalty/editTemplate':
				if ($acl->sruAdmin('penalty', 'editOne', $get->penaltyId)) {
					return 'SruAdmin_PenaltyTemplateChange';
				} else {
					return 'Sru_Error403';
				}
			case 'penalties/penalty/history':
				return 'SruAdmin_PenaltyHistory';
			default:
				return 'Sru_Error404';
		}
	}
}
