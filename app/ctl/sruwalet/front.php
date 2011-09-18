<?
/**
 * front controller modulu Walet
 */
class UFctl_SruWalet_Front
extends UFctl {

	protected function parseParameters() {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$acl = $this->_srv->get('acl');

		$segCount = $req->segmentsCount();
		if (0 == $segCount) {
			$get->view = 'main';
		} else {
			switch ($req->segment(1)) {
				case 'users':
					if ($segCount == 1) {
						$get->view = 'users/main';
					} else {
						switch ($req->segment(2)) {
							case 'search':
								$get->view = 'users/search';
								for ($i = 3; $i <= $segCount; ++$i) {
									$tmp = explode(':', $req->segment($i), 2);
									switch ($tmp[0]) {
										case 'surname':
											$get->searchedSurname = urldecode($tmp[1]);
											break;
										case 'registryNo':
											$get->searchedRegistryNo = urldecode($tmp[1]);
											break;
										case 'pesel':
											$get->searchedPesel = urldecode($tmp[1]);
											break;
									}
								}
								break;
							case 'quicksearch':
								if ($segCount > 2) {
									$get->searchedSurname = urldecode($req->segment(3));
								}
								$get->view = 'users/quicksearch';
								break;
							case 'quickcountrysearch':
								if ($segCount > 2) {
									$get->searchedCountry = urldecode($req->segment(3));
								}
								$get->view = 'users/quickcountrysearch';
								break;
							case 'validatepesel':
								if ($segCount > 2) {
									$get->peselToValidate = urldecode($req->segment(3));
								}
								$get->view = 'users/validatepesel';
								break;
							case ':add':
								$get->view = 'users/user/add';
								for ($i = 3; $i <= $segCount; ++$i) {
									$tmp = explode(':', $req->segment($i), 2);
									switch ($tmp[0]) {
										case 'surname':
											$get->inputSurname = urldecode($tmp[1]);
											break;
										case 'registryNo':
											$get->inputRegistryNo = urldecode($tmp[1]);
											break;
										case 'pesel':
											$get->inputPesel = urldecode($tmp[1]);
											break;
									}
								}
								break;
							default:
								$get->view = 'users/user';
								$id = (int)$req->segment(2);
								if ($id <= 0) {
									$get->view = 'error404';
									break;
								}
								$get->userId = $id;
								if ($segCount > 2) {
									switch ($req->segment(3)) {
										case 'history':
											$get->view = 'users/user/history';
											break;
										case ':edit':
											$get->view = 'users/user/edit';
											break;
										case ':del':
											$get->view = 'users/user/del';
											break;
										case ':print':
											$get->view = 'users/user/print';
											if ($segCount > 3) {
												$get->password = $req->segment(4);
											}
											break;
										default:
											$get->view = 'error404';
											break;
									}
								}
								break;
						}
					}
					break;
				case 'nations':
					$get->view = 'nations/main';
					if ($segCount > 1) {
						switch ($req->segment(2)) {
							case 'quicksave':
								if ($segCount != 4) {
									$get->view = 'error404';
								} else {
									$id = (int)$req->segment(3);
									$name = $req->segment(4);
									$get->view = 'nations/quicksave';
									$get->nationId = $id;
									$get->nationName = $name;
								}
								break;
							default:
								$get->view = 'error404';
								break;
						}
					}
					break;
				case 'inhabitants':
					$get->view = 'inhabitants/main';
					break;
				case 'dormitories':
					if ($segCount == 1) {
						$get->view = 'error404';
					} else {
						$alias = $req->segment(2);  
						$get->dormAlias = $alias;
						if($segCount == 2) {
							$get->view = 'dormitories/dorm';
						} else {
							switch ($req->segment(3)) {
								case ':dormexport':
									$get->view = 'dormitories/dorm/docdormexport';
									break;
								case ':usersexport':
									$get->view = 'dormitories/dorm/docusersexport';
									break;
								case ':regbookexport':
									$get->view = 'dormitories/dorm/docregbookexport';
									break;
								case ':dormexcelexport':
									$get->view = 'dormitories/dorm/xlsdormexport';
									break;
								case ':usersexcelexport':
									$get->view = 'dormitories/dorm/xlsusersexport';
									break;
								case ':regbookexcelexport':
									$get->view = 'dormitories/dorm/xlsregbookexport';
									break;
								default:
									$get->view = 'error404';
									break;
							}
						}
					}
					break;
				case 'stats':
					if (1 == $segCount) {
						$get->view = 'stats/users';
						break;
					} else {
						switch ($req->segment(2)) {
							case 'dormitories':
								$get->view = 'stats/dormitories';
								break;
							case ':dormitoriesexport':
								$get->view = 'stats/docdormitoriesexport';
								break;
							case ':usersexport':
								$get->view = 'stats/docusersexport';
								break;
							default:
								$get->view = 'error404';
								break;
						}
					}
					break;
				case 'admins':
					if (1 == $segCount) {
						$get->view = 'admins/main';
						break;
					} else {
						switch ($req->segment(2)) {
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
					break;
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

		if ($post->is('adminLogout') && $acl->sruWalet('admin', 'logout')) {
			$act = 'Admin_Logout';
		} elseif ($post->is('adminLogin') && $acl->sruWalet('admin', 'login')) {
			$act = 'Admin_Login';
		} elseif ($post->is('userSearch')) {
			$act = 'User_Search';
		} elseif ('users/user/add' == $get->view && $post->is('userAdd') && $acl->sruWalet('user', 'add')) {
			$act = 'User_Add';
		} elseif ($post->is('userEdit') && $acl->sruWalet('user', 'edit', $get->userId)) {
			$act = 'User_Edit';
		} elseif ('users/user/del' == $get->view && $post->is('userDel') && $acl->sruWalet('user', 'del', $get->userId)) {
			$act = 'User_Del';
		} elseif ('admins/add' == $get->view && $post->is('adminAdd') && $acl->sruWalet('admin', 'add')) {
			$act = 'Admin_Add';
		} elseif ('admins/edit' == $get->view && $post->is('adminEdit') && $acl->sruWalet('admin', 'edit', $get->adminId)) {
			$act = 'Admin_Edit';
		}

		if (isset($act)) {
			$action = 'SruWalet_'.$act;
		}

		return $action;
	}

	protected function chooseView($view = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$post= $req->post;
		$msg = $this->_srv->get('msg');
		$acl = $this->_srv->get('acl');

		if (!$acl->sruWalet('admin', 'logout')) {
			return 'SruWalet_Login';
		}
		switch ($get->view) {
			case 'main':
				if ($acl->sruWalet('admin', 'logout')) {
					return 'SruWalet_Main';
				} else {
					return 'SruWalet_Login';
				}
			case 'users/main':
				return 'SruWalet_Main';
			case 'users/search':
				return 'SruWalet_UserSearchResults';
			case 'users/quicksearch':
				return 'SruWalet_UserQuickSearch';
			case 'users/quickcountrysearch':
				return 'SruWalet_CountryQuickSearch';
			case 'users/validatepesel':
				return 'SruWalet_UserValidatePesel';
			case 'users/user':
				if ($acl->sruWalet('user', 'view', $get->userId)) {
					return 'SruWalet_User';
				} else {
					return 'Sru_Error403';
				}
			case 'users/user/history':
				return 'SruWalet_UserHistory';
			case 'users/user/servicehistory':
				return 'SruWalet_ServiceHistory';
			case 'users/user/edit':
				$user = UFra::factory('UFbean_Sru_User');
				$user->getByPK($get->userId);
				if($user->documentNumber == '' || $user->nationality == '' ||
					$user->address == '' || ($user->nationality == 1 && $user->pesel == '') || 
					(in_array($user->typeId, UFra::shared('UFconf_Sru')->mustBeRegistryNo) && $user->registryNo == ''))
					{
						$msg->del('userEdit/ok');
						$msg->set('userEdit/warn');
						$msg = $this->_srv->get('msg');
					}
				if ($msg->get('userEdit/ok')) {
					return 'SruWalet_User';
				} else if($msg->get('userEdit/warn') && !$msg->get('userEdit/ok') && isset($_POST['submit']) // isset($post->submit) zawsze zwraca false O.O 
							&& !$msg->get('userEdit/errors')) {
					return 'SruWalet_User';
				} else if ($acl->sruWalet('user', 'edit', $get->userId)) {
					return 'SruWalet_UserEdit';
				} else {
					return 'Sru_Error403';
				}
			case 'users/user/del':
				if ($msg->get('userDel/ok')) {
					return 'SruWalet_Main';
				} elseif ($acl->sruWalet('user', 'del', $get->userId)) {
					return 'SruWalet_UserDel';
				} else {
					return 'Sru_Error403';
				}
			case 'users/user/print':
				return 'SruWalet_UserPrint';
			case 'users/user/add':
				$user = UFra::factory('UFbean_Sru_User');
				try{
					$user->getByPK($get->userId);
					if($user->documentNumber == '' || $user->nationality == '' ||
						$user->address == '' || ($user->nationality == 1 && $user->pesel == '') || 
						(in_array($user->typeId, UFra::shared('UFconf_Sru')->mustBeRegistryNo) && $user->registryNo == ''))
						{
							$msg->del('userEdit/ok');
							$msg->set('userEdit/warn');
							$msg = $this->_srv->get('msg');
						}
				}catch(Exception $e){}
				if ($msg->get('userAdd/ok')) {
					return 'SruWalet_User';
				} else if($msg->get('userEdit/warn') && !$msg->get('userEdit/ok') && isset($_POST['submit']) // isset($post->submit) zawsze zwraca false O.O 
							&& !$msg->get('userEdit/errors')) {
					return 'SruWalet_User';
				} else {
					return 'SruWalet_UserAdd';
				}
			case 'nations/main':
				return 'SruWalet_Nations';
			case 'nations/quicksave':
				return 'SruWalet_NationQuickSave';
			case 'inhabitants/main':
				return 'SruWalet_Inhabitants';
			case 'dormitories/dorm':
				if ($acl->sruWalet('dorm', 'view', $get->dormAlias)) {
					return 'SruWalet_Dorm';
				} else {
					return 'Sru_Error403';
				}
			case 'dormitories/dorm/docdormexport':
				if ($acl->sruWalet('dorm', 'view', $get->dormAlias)) {
					return 'SruWalet_DormDocExport';
				} else {
					return 'Sru_Error403';
				}
			case 'dormitories/dorm/docusersexport':
				if ($acl->sruWalet('dorm', 'view', $get->dormAlias)) {
					return 'SruWalet_DormUsersDocExport';
				} else {
					return 'Sru_Error403';
				}
			case 'dormitories/dorm/docregbookexport':
				if ($acl->sruWalet('dorm', 'view', $get->dormAlias)) {
					return 'SruWalet_DormRegBookDocExport';
				} else {
					return 'Sru_Error403';
				}
			case 'dormitories/dorm/xlsdormexport':
				if ($acl->sruWalet('dorm', 'view', $get->dormAlias)) {
					return 'SruWalet_DormXlsExport';
				} else {
					return 'Sru_Error403';
				}
			case 'dormitories/dorm/xlsusersexport':
				if ($acl->sruWalet('dorm', 'view', $get->dormAlias)) {
					return 'SruWalet_DormUsersXlsExport';
				} else {
					return 'Sru_Error403';
				}
			case 'dormitories/dorm/xlsregbookexport':
				if ($acl->sruWalet('dorm', 'view', $get->dormAlias)) {
					return 'SruWalet_DormRegBookXlsExport';
				} else {
					return 'Sru_Error403';
				}
			case 'stats/users':
				return 'SruWalet_StatsUsers';
			case 'stats/dormitories':
				return 'SruWalet_StatsDormitories';
			case 'stats/docusersexport':
				return 'SruWalet_StatsUsersDocExport';
			case 'stats/docdormitoriesexport':
				return 'SruWalet_StatsDormitoriesDocExport';
			case 'admins/main':
				return 'SruWalet_Admins';
			case 'admins/admin':
				return 'SruWalet_Admin';
			case 'admins/add':
				if ($msg->get('adminAdd/ok')) {
					return 'SruWalet_Admins';
				} elseif ($acl->sruWalet('admin', 'add')) {
					return 'SruWalet_AdminAdd';
				} else {
					return 'Sru_Error404';
				}
			case 'admins/edit':
				if ($msg->get('adminEdit/ok')) { 
					return 'SruWalet_Admin';
				} elseif ($acl->sruWalet('admin', 'edit', $get->adminId)) {
					return 'SruWalet_AdminEdit';
				} else {
					return 'Sru_Error403';
				}
			default:
				return 'Sru_Error404';
		}
	}
}
