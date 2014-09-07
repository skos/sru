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

		try {
			$admin = UFra::factory('UFbean_SruWalet_Admin');
			$admin->getFromSession();

			if ($this->_srv->get('session')->typeIdWalet != UFacl_SruWalet_Admin::PORTIER
				&& (is_null($admin->lastPswChange) == true || time() - $admin->lastPswChange > UFra::shared('UFconf_Sru')->passwordValidTime)) {
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
							case 'checkregistryno':
								if ($segCount > 2) {
									$get->registryNoToCheck = urldecode($req->segment(3));
								}
								$get->view = 'users/checkregistryno';
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
				case 'inventory':
					$get->view = 'inventory/main';
					break;
				case 'dormitories':
					if ($segCount == 1) {
						$get->view = 'error404';
					} else {
						$alias = $req->segment(2);
						$get->dormAlias = $alias;
						if ($segCount == 2) {
							$get->view = 'dormitories/dorm';
						} else if ($segCount == 4) {
							$alias = $req->segment(3);
							$get->roomAlias = $alias;
							if ($req->segment(4) == ':edit') {
								$get->view = 'dormitories/room/edit';
							} else {
								$get->view = 'error404';
							}
						} else {
							$get->view = 'error404';
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
											$get->view = 'admins/admin/edit';
											break;
										case 'history':
											$get->view = 'admins/admin/history';
											break;
										default:
											$get->view = 'error404';
											break;
									}
								}
						}
					}
					break;
				case 'logout':
					$ctl = UFra::factory('UFact_SruWalet_Admin_Logout');
					$ctl->go();
					UFlib_Http::redirect(UFURL_BASE.'/'.implode('/', $this->_srv->get('req')->segments(0)));
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
		} elseif ('admins/admin/edit' == $get->view && $post->is('adminEdit') && $acl->sruWalet('admin', 'edit', $get->adminId)) {
			$act = 'Admin_Edit';
		} elseif ('dormitories/dorm' == $get->view && $post->is('docExport') && $acl->sruWalet('dorm', 'view', $get->dormAlias)) {
			$act = 'Doc_Export';
		} elseif ('dormitories/room/edit' == $get->view && $post->is('roomEdit') && $acl->sruWalet('room', 'edit')) {
			$act = 'Room_Edit';
		} elseif ('adminOwnPswEdit' == $get->view && $post->is('adminOwnPswEdit') && $acl->sruWalet('admin', 'edit', $get->adminId)) {
			$act = 'Admin_OwnPswEdit';
		}
                  elseif ('dormitories/dorm' == $get->view && $post->is('allUsersDel') && $acl->sruWalet('dorm', 'checkout', $get->dormAlias)) {
			$act = 'User_AllDel';
                }

		if (isset($act)) {
			$action = 'SruWalet_'.$act;
		}

		return $action;
	}

	protected function chooseView($view = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$post = $req->post;
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
			case 'users/checkregistryno':
				return 'SruWalet_UserCheckRegistryNo';
			case 'users/user':
				if ($acl->sruWalet('user', 'view', $get->userId)) {
					return 'SruWalet_User';
				} else {
					return 'Sru_Error403';
				}
			case 'users/user/history':
				return 'SruWalet_UserHistory';
			case 'users/user/edit':
				$user = UFra::factory('UFbean_Sru_User');
				$user->getByPK($get->userId);
				if (!$this->validateUserDataCompleteness($user) && $msg->get('userEdit/ok')) {
					$msg->set('userEdit/warn');
					$msg = $this->_srv->get('msg');
				}
				if ($msg->get('userEdit/ok')) {
					return 'SruWalet_User';
				} else if ($acl->sruWalet('user', 'edit', $get->userId)) {
					$msg->del('userEdit/warn');
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
				if ($acl->sruWalet('user', 'add')) {
					$user = UFra::factory('UFbean_Sru_User');
					try {
						$user->getByPK($get->userId);
						if (!$this->validateUserDataCompleteness($user) && $msg->get('userAdd/ok')) {
							$msg->set('userAdd/warn');
							$msg = $this->_srv->get('msg');
						}
					} catch(Exception $e) {}
					if ($msg->get('userAdd/ok')) {
						return 'SruWalet_User';
					} else {
						return 'SruWalet_UserAdd';
					}
				} else {
					return 'Sru_Error403';
				}
			case 'nations/main':
				if ($acl->sruWalet('country', 'view')) {
					return 'SruWalet_Nations';
				} else {
					return 'Sru_Error403';
				}
			case 'nations/quicksave':
				if ($acl->sruWalet('country', 'view')) {
					return 'SruWalet_NationQuickSave';
				} else {
					return 'Sru_Error403';
				}
			case 'inhabitants/main':
				return 'SruWalet_Inhabitants';
			case 'inventory/main':
				if ($acl->sruWalet('inventory', 'view')) {
					return 'SruWalet_Inventory';
				} else {
					return 'Sru_Error403';
				}
			case 'dormitories/dorm':
				if ($msg->get('docExport/ok') && (!is_null($get->docCode))) {
					return 'SruWalet_'.$get->docCode.'Export';
				} else if ($acl->sruWalet('dorm', 'view', $get->dormAlias)) {
					return 'SruWalet_Dorm';
				} else {
					return 'Sru_Error403';
				}
			case 'dormitories/room/edit':
				if ($msg->get('roomEdit/ok')) {
					return 'SruWalet_Dorm';
				} else if ($acl->sruWalet('room', 'edit')) {
					return 'SruWalet_RoomEdit';
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
				if ($acl->sruWalet('admin', 'view')) {
					return 'SruWalet_Admins';
				} else {
					return 'Sru_Error404';
				}
			case 'admins/admin':
				if ($acl->sruWalet('admin', 'view')) {
					return 'SruWalet_Admin';
				} else {
					return 'Sru_Error404';
				}
			case 'admins/admin/history':
				if ($acl->sruWalet('admin', 'view')) {
					return 'SruWalet_AdminHistory';
				} else {
					return 'Sru_Error404';
				}
			case 'admins/add':
				if ($msg->get('adminAdd/ok') && $acl->sruWalet('country', 'view')) {
					return 'SruWalet_Admins';
				} elseif ($acl->sruWalet('admin', 'add')) {
					return 'SruWalet_AdminAdd';
				} else {
					return 'Sru_Error404';
				}
			case 'admins/admin/edit':
				if ($msg->get('adminEdit/ok') && $acl->sruWalet('country', 'view')) {
					return 'SruWalet_Admin';
				} elseif ($acl->sruWalet('admin', 'edit', $get->adminId)) {
					return 'SruWalet_AdminEdit';
				} else {
					return 'Sru_Error403';
				}
			case 'adminOwnPswEdit':
				return 'SruWalet_AdminOwnPswEdit';
			default:
				return 'Sru_Error404';
		}
	}

	private function validateUserDataCompleteness($user) {
		if (($user->documentNumber == '' && $user->documentType != UFbean_Sru_User::DOC_TYPE_NONE) || $user->nationality == '' ||
			$user->address == '' || ($user->nationality == 1 && $user->pesel == '') ||
			(in_array($user->typeId, UFra::shared('UFconf_Sru')->mustBeRegistryNo) && $user->registryNo == '')) {
			return false;
		} else {
			return true;
		}
	}
}