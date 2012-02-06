<?
/**
 * front controller czesci administracyjnej sru dotyczacej komputerow
 */
class UFctl_SruAdmin_Computers
extends UFctl {

	protected function parseParameters() {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$acl = $this->_srv->get('acl');
		
		$segCount = $req->segmentsCount();

		// wyzsze segmenty sprawdzane sa w front'ie
		if (1 == $segCount) {
			$get->view = 'computers/main';
		} else {
			switch ($req->segment(2)) {
				case 'search':
					$get->view = 'computers/search';
					for ($i=3; $i<=$segCount; ++$i) {
						
						$tmp = explode(':', $req->segment($i), 2);
						switch ($tmp[0]) {
							case 'typeId':
								$get->searchedTypeId = urldecode($tmp[1]);
								break;
							case 'host':
								$get->searchedHost = urldecode($tmp[1]);
								break;
							case 'ip':
								$get->searchedIp = urldecode($tmp[1]);
								break;
							case 'mac':
								$get->searchedMac = urldecode($tmp[1]);
								break;
							case 'computersActive':
								$get->searchedActive = true;
								break;
						}
					}
					break;
				default:
					$get->view = 'computers/computer';
					$id = (int)$req->segment(2);
					if ($id <= 0) {
						$get->view = 'error404';
						break;
					}
					$get->computerId = $id;
					if ($segCount > 2) {
						switch ($req->segment(3)) {
							case 'history':
								$get->view = 'computers/computer/history';
								break;
							case 'stats':
								$get->view = 'computers/computer/stats';
								break;
							case ':edit':
								$get->view = 'computers/computer/edit';
								if ($segCount > 3) {
									$ver = (int)$req->segment(4);
									if ($ver > 0) {
										$get->view = 'computers/computer/restore';
										$get->computerHistoryId = $ver;
									}
								}
								break;
							case ':aliases':
								$get->view = 'computers/computer/aliases';
								break;
							case ':del':
								$get->view = 'computers/computer/delete';
								break;	
							case 'penalties':
								$get->view = 'computers/computer/penalties';
								break;				
							default:
								$get->view = 'error404';
								break;
						}
					}
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
		} elseif ($post->is('computerSearch')) {
			$act = 'Computer_Search';
		} elseif ($post->is('computerEdit') && $acl->sruAdmin('computer', 'edit')) {
			$act = 'Computer_Edit';
		} elseif ($post->is('computerAliasesEdit') && $acl->sruAdmin('computer', 'editAliases')) {
			$act = 'Computer_AliasesEdit';
		} elseif ('computers/computer/delete' == $get->view && $post->is('computerDel') ) {
			$act = 'Computer_Del';
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
		switch ($get->view) {
			case 'computers/main':
				return 'SruAdmin_Computers';
			case 'computers/search':
				return 'SruAdmin_ComputerSearchResults';
			case 'computers/computer':
				return 'SruAdmin_Computer';
			case 'computers/computer/history':
				return 'SruAdmin_ComputerHistory';
			case 'computers/computer/stats':
				try {
					$get->statHour = $post->transferStats['statHour'];
					$get->statDate = $post->transferStats['statDate'];
				} catch (UFex_Core_DataNotFound $ex) {
					$get->statHour = date('H:i');
					$get->statDate = date('Ymd');
				}
				return 'SruAdmin_ComputerStats';
			case 'computers/computer/delete':
				if ($msg->get('computerDel/ok')) {
					return 'SruAdmin_Computer';
				} else {
					return 'SruAdmin_ComputerDelete';
				}
			case 'computers/computer/edit':
				if ($msg->get('computerDel/ok')) {
					return 'SruAdmin_Main';
				} else {
					return 'SruAdmin_ComputerEdit';
				}
			case 'computers/computer/aliases':
				if ($msg->get('computerAliasesEdit/ok')) {
					return 'SruAdmin_Computer';
				} else {
					return 'SruAdmin_ComputerAliasesEdit';
				}
			case 'computers/computer/restore':
				return 'SruAdmin_ComputerRestore';
			case 'computers/computer/penalties':
				return 'SruAdmin_ComputerPenalties';
			default:
				return 'Sru_Error404';
		}
	}
}
