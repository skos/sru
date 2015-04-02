<?

/**
 * Walet
 */
class UFbox_SruWalet
extends UFbox {

	protected function _getAdminFromGet() {
		$bean = UFra::factory('UFbean_SruWalet_Admin');
		$bean->getByPK((int)$this->_srv->get('req')->get->adminId);

		return $bean;
	}

	protected function _getUserFromGet() {
		$bean = UFra::factory('UFbean_Sru_User');
		$bean->getByPK((int)$this->_srv->get('req')->get->userId);

		return $bean;
	}

	protected function _getDormFromGet() {
		$bean = UFra::factory('UFbean_Sru_Dormitory');
		$bean->getByAlias($this->_srv->get('req')->get->dormAlias);
		
		return $bean;
	}
	
	protected function _getRoomFromGet() {
		$bean = UFra::factory('UFbean_SruAdmin_Room');
		$bean->getByAlias($this->_srv->get('req')->get->dormAlias, $this->_srv->get('req')->get->roomAlias);

		return $bean;
	}

	public function login() {
		$bean = UFra::factory('UFbean_SruWalet_Admin');

		$d['admin'] = $bean;

		return $this->render(__FUNCTION__, $d);
	}

	public function logout() {
		try{
			$bean = UFra::factory('UFbean_SruWalet_Admin');
			$bean->getFromSession();

			$d['admin'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function waletBar() {
		try {
			$bean = UFra::factory('UFbean_SruWalet_Admin');
			$bean->getFromSession();
			$d['admin'] = $bean;


			$sess = $this->_srv->get('session');
			try {
				$d['lastLoginIp'] = $sess->lastLoginIpWalet;
			} catch (UFex_Core_DataNotFound $e) {
				$d['lastLoginIp'] = null;
			}
			try {
				$d['lastLoginAt'] = $sess->lastLoginAtWalet;
			} catch (UFex_Core_DataNotFound $e) {
				$d['lastLoginAt'] = null;
			}
			try {
				$d['lastInvLoginIp'] = $sess->lastInvLoginIpWalet;
			} catch (UFex_Core_DataNotFound $e) {
				$d['lastInvLoginIp'] = null;
			}
			try {
				$d['lastInvLoginAt'] = $sess->lastInvLoginAtWalet;
			} catch (UFex_Core_DataNotFound $e) {
				$d['lastInvLoginAt'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	/* Mieszkańcy */

	public function userSearch() {
		$bean = UFra::factory('UFbean_Sru_User');

		$d['user'] = $bean;

		$get = $this->_srv->get('req')->get;
		$tmp = array();
		try {
			$tmp['surname'] = $get->searchedSurname;
		} catch (UFex_Core_DataNotFound $e) {
		}
		try {
			$tmp['registryNo'] = $get->searchedRegistryNo;
		} catch (UFex_Core_DataNotFound $e) {
		}
		try {
			$tmp['pesel'] = $get->searchedPesel;
		} catch (UFex_Core_DataNotFound $e) {
		}
		$d['searched'] = $tmp;

		return $this->render(__FUNCTION__, $d);
	}

	public function addUserLink() {
		try {
			$get = $this->_srv->get('req')->get;
			$tmp = array();
			try {
				$tmp['surname'] = $get->searchedSurname;
			} catch (UFex_Core_DataNotFound $e) {
			}
			try {
				$tmp['registryNo'] = $get->searchedRegistryNo;
			} catch (UFex_Core_DataNotFound $e) {
			}
			try {
				$tmp['pesel'] = $get->searchedPesel;
			} catch (UFex_Core_DataNotFound $e) {
			}
			$d['searched'] = $tmp;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('userSearchResultsNotFound');
		}
	}

	public function userSearchResults() {
		try {
			$bean = UFra::factory('UFbean_Sru_UserList');

			$get = $this->_srv->get('req')->get;
			$tmp = array();
			try {
				$tmp['surname'] = $get->searchedSurname;
			} catch (UFex_Core_DataNotFound $e) {
			}
			try {
				$tmp['registryNo'] = $get->searchedRegistryNo;
			} catch (UFex_Core_DataNotFound $e) {
			}
			try {
				$tmp['pesel'] = $get->searchedPesel;
			} catch (UFex_Core_DataNotFound $e) {
			}
			
			$acl = UFra::factory('UFacl_SruWalet_Admin');
			$activeOnly = false;
			if(!$acl->view()) {
				$activeOnly = true;
			}
			$bean->search($tmp, true, $activeOnly);
			if (1 == count($bean)) {
				$get->userId = $bean[0]['id'];
				return $this->user();
			}

			$d['users'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function quickUserSearchResults() {
		try {
			$bean = UFra::factory('UFbean_Sru_UserList');

			$get = $this->_srv->get('req')->get;
			$tmp = array();
			try {
				$tmp['surname'] = strtolower($get->searchedSurname).'*';
			} catch (UFex_Core_DataNotFound $e) {
			}
			$bean->quickSearch($tmp);

			$d['users'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function quickCountrySearchResults() {
		try {
			$bean = UFra::factory('UFbean_SruWalet_CountryList');

			$get = $this->_srv->get('req')->get;
			$tmp = array();
			try {
				$tmp['nationality'] = strtolower($get->searchedCountry).'*';
			} catch (UFex_Core_DataNotFound $e) {
			}
			$bean->quickSearch($tmp);

			$d['countries'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
        
        public function checkRegistryNoResults() {
		if ($this->_srv->get('req')->get->is('registryNoToCheck')) {
			$get = $this->_srv->get('req')->get;
			$d['registryNo'] = $get->registryNoToCheck;
			return $this->render(__FUNCTION__, $d);
		}
		return $this->render(__FUNCTION__.'NotFound', array());
	}

	public function toDoList() {
		try {
			$admin = UFra::factory('UFbean_SruWalet_Admin');
			$admin->getFromSession();
			$d['admin'] = $admin;

			if ($admin->typeId != UFacl_SruWalet_Admin::HEAD) {
				try {
					$dorms = UFra::factory('UFbean_SruWalet_AdminDormitoryList');
					$dorms->listAllById($admin->id);
				} catch (UFex_Dao_NotFound $e) {
					$dorms = null;
				}
			} else {
				$dorms = UFra::factory('UFbean_Sru_DormitoryList');
				$dorms->listAllForWalet();
			}

			$d['users'] = null;
			if (!is_null($dorms)) {
				foreach ($dorms as $dorm) {
					try {
						$users = UFra::factory('UFbean_Sru_UserList');
						$users->listActiveWithoutCompulsoryDataByDorm($dorm['dormitoryId']);

						$d['users'][$dorm['dormitoryId']] = $users;
					} catch (UFex_Dao_NotFound $e) {
						$d['users'][$dorm['dormitoryId']] = null;
					}
				}
			}
			$userCount = 0;
			if (!is_null($d['users'])) {
				foreach ($d['users'] as $dorm) {
					$userCount += count($dorm);
				}
			}
			if ($userCount == 0) {
				$d['users'] = null;
			}
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function titleUser() {
		try {
			$bean = $this->_getUserFromGet();

			$d['user'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function user() {
		try {
			$bean = $this->_getUserFromGet();

			$d['user'] = $bean;
			
			try {
				$functions = UFra::factory('UFbean_Sru_UserFunctionList');
				$functions->listByUserId($bean->id);
				
				$d['functions'] = $functions;
			} catch (UFex_Dao_NotFound $e) {
				$d['functions'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function userEdit() {
		try {
			$faculties = UFra::factory('UFbean_Sru_FacultyList');
			$faculties->listAll();
			
			$bean = $this->_getUserFromGet();

			$admin = UFra::factory('UFbean_SruWalet_Admin');
			$admin->getFromSession();
			if ($admin->typeId != UFacl_SruWalet_Admin::HEAD) {
				try {
					$dorms = UFra::factory('UFbean_SruWalet_AdminDormitoryList');
					$dorms->listAllById($admin->id);
				} catch (UFex_Dao_NotFound $e) {
					$dorms = null;
				}
			} else {
				$dorms = UFra::factory('UFbean_Sru_DormitoryList');
				$dorms->listAllForWalet();
			}

			$d['user'] = $bean;
			$d['dormitories'] = $dorms;
			$d['faculties'] = $faculties;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('userNotFound');
		}
	}

	public function titleUserEdit() {
		try {
			$bean = $this->_getUserFromGet();

			$d['user'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}
	
	public function userFunctionsEdit() {
		try {
			$bean = $this->_getUserFromGet();
			
			try {
				$functions = UFra::factory('UFbean_Sru_UserFunctionList');
				$functions->listByUserId($bean->id);
				
				$d['functions'] = $functions;
			} catch (UFex_Dao_NotFound $e) {
				$d['functions'] = null;
			}
			
			$d['user'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('userNotFound');
		}
	}

	public function titleUserFunctionsEdit() {
		try {
			$bean = $this->_getUserFromGet();

			$d['user'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function userDel() {
		try {
			$bean = $this->_getUserFromGet();
			$d['user'] = $bean;
			
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('userNotFound');
		}
	}

	public function userHistory() {
		try {
			$bean = $this->_getUserFromGet();
			$d['user'] = $bean;
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}

		$history = UFra::factory('UFbean_SruAdmin_UserHistoryList');
		try {
			$history->listByUserId($bean->id);
		} catch (UFex_Dao_NotFound $e) {
		}
		$d['history'] = $history;

		return $this->render(__FUNCTION__, $d);
	}

	public function userAdd() {
		try {
			$faculties = UFra::factory('UFbean_Sru_FacultyList');
			$faculties->listAll();

			$admin = UFra::factory('UFbean_SruWalet_Admin');
			$admin->getFromSession();
			if ($admin->typeId != UFacl_SruWalet_Admin::HEAD) {
				try {
					$dorms = UFra::factory('UFbean_SruWalet_AdminDormitoryList');
					$dorms->listAllById($admin->id);
				} catch (UFex_Dao_NotFound $e) {
					$dorms = null;
				}
			} else {
				$dorms = UFra::factory('UFbean_Sru_DormitoryList');
				$dorms->listAllForWalet();
			}

			$bean = UFra::factory('UFbean_Sru_User');

			$get = $this->_srv->get('req')->get;
			$tmp = array();
			try {
				$d['surname'] = mb_convert_case($get->inputSurname, MB_CASE_TITLE, "UTF-8");
			} catch (UFex_Core_DataNotFound $e) {
				$d['surname'] = null;
			}
			try {
				$d['registryNo'] = $get->inputRegistryNo;
			} catch (UFex_Core_DataNotFound $e) {
				$d['registryNo'] = null;
			}
			try {
				$d['pesel'] = $get->inputPesel;
			} catch (UFex_Core_DataNotFound $e) {
				$d['pesel'] = null;
			}
	
			$d['user'] = $bean;
			$d['dormitories'] = $dorms;
			$d['faculties'] = $faculties;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function userPrint() {
		try {
                        $sess = $this->_srv->get('session');
			try {
				$bean = $this->_getUserFromGet();
				$d['user'] = $bean;
                                $sess->lang=$d['user']->lang;
			} catch (UFex_Core_DataNotFound $e) {
				return $this->render(__FUNCTION__.'Error');
			}
			try {
				$d['password'] = $this->_srv->get('req')->get->password;
			} catch (UFex_Core_DataNotFound $e) {
				$d['password'] = null;
			}

			$conf = UFra::shared('UFconf_Sru');
			if ($bean->typeId == UFbean_Sru_User::TYPE_TOURIST_INDIVIDUAL) {
				$d['userPrintWaletText'] = $conf->touristPrintWaletText;
				$d['userPrintSkosText'] = $conf->touristPrintSkosText;
			} else {
				$d['userPrintWaletText'] = $conf->userPrintWaletText;
				$d['userPrintSkosText'] = $conf->userPrintSkosText;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
	
	/* Narodowości */

	public function nations() {
		try {
			$countries = UFra::factory('UFbean_SruWalet_CountryList');
			$countries->listAll();
			$d['countries'] = $countries;
		
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound', $d);
		}
	}
	
	public function quickNationSaveResults() {
		try {
			$get = $this->_srv->get('req')->get;
			
			$bean = UFra::factory('UFbean_SruWalet_Country');
			$bean->getByPk($get->nationId);
			$bean->nationality = htmlspecialchars(urldecode($get->nationName));
			$bean->save();
			$d['nation'] = $bean->nationality;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex $e) {
			UFlib_Http::notFound();
			return '';
		}
	}

	/* Obsadzenie */

	public function inhabitants() {
		try {
			$dorms = UFra::factory('UFbean_Sru_DormitoryList');
			$dorms->listAll();
			$d['dormitories'] = $dorms;
			try {
				$rooms = UFra::factory('UFbean_SruAdmin_RoomList');
				$rooms->listAllOrdered(); 

				$d['rooms'] = $rooms;
			} catch (UFex_Dao_NotFound $e) {
				$d['rooms'] = null;
			}
			
			try {
				$functions = UFra::factory('UFbean_Sru_UserFunctionList');
				$functions->listByDormitoryId(null); 

				$d['functions'] = $functions;
			} catch (UFex_Dao_NotFound $e) {
				$d['functions'] = null;
			}
		
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound', $d);
		}
	}

	public function titleDorm() {
		try {
			$bean = $this->_getDormFromGet();

			$d['dorm'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function dorm() {
		try {
			$bean = $this->_getDormFromGet();
			$d['dorm'] = $bean;
			
			try {
				$rooms = UFra::factory('UFbean_SruAdmin_RoomList');
				$rooms->listByDormitoryId($bean->id, true); 
				
				$d['rooms'] = $rooms;
			} catch (UFex_Dao_NotFound $e) {
				$d['rooms'] = null;
			}

			try {
				$users = UFra::factory('UFbean_Sru_UserList');
				$users->listActiveByDorm($bean->id);
				
				$d['users'] = $users;
			} catch (UFex_Dao_NotFound $e) {
				$d['users'] = null;
			}
			
			try {
				$toDelete = UFra::factory('UFbean_Sru_UserList');
				$toDelete->listActiveByDorm($bean->id, true);
				
				$markedToDelete = 0;
				$availableForDelete = 0;
				foreach ($toDelete as $user) {
					if ($user['toDeactivate']) {
						$markedToDelete++;
					} else {
						$availableForDelete++;
					}
				}
				$d['markedToDelete'] = $markedToDelete;
				$d['availableForDelete'] = $availableForDelete;
			} catch (UFex_Dao_NotFound $e) {
				$d['markedToDelete'] = 0;
				$d['availableForDelete'] = 0;
			}
			
			try {
				$functions = UFra::factory('UFbean_Sru_UserFunctionList');
				$functions->listByDormitoryId($bean->id); 

				$d['functions'] = $functions;
			} catch (UFex_Dao_NotFound $e) {
				$d['functions'] = null;
			}
		
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function titleDormExport() {
		try {
			$bean = $this->_getDormFromGet();

			$d['dorm'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('inhabitantsNotFound');
		}
	}

	public function dormExport() {
		try {
			$bean = $this->_getDormFromGet();
			$d['dorm'] = $bean;
			
			try {
				$rooms = UFra::factory('UFbean_SruAdmin_RoomList');
				$rooms->listByDormitoryId($bean->id, true); 
				
				$d['rooms'] = $rooms;
			} catch (UFex_Dao_NotFound $e) {
				$d['rooms'] = null;
			}

			try {
				$users = UFra::factory('UFbean_Sru_UserList');
				$users->listActiveByDorm($bean->id);
				
				$d['users'] = $users;
			} catch (UFex_Dao_NotFound $e) {
				$d['users'] = null;
			}
				
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('inhabitantsNotFound');
		}
	}

	public function titleDormUsersExport() {
		try {
			$bean = $this->_getDormFromGet();
			$d['dorm'] = $bean;
			
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('inhabitantsNotFound');
		}
	}

	public function dormUsersExport() {
		try {
			$bean = $this->_getDormFromGet();
			$d['dorm'] = $bean;

			try {
				$users = UFra::factory('UFbean_Sru_UserList');
				$users->listActiveByDorm($bean->id);
				
				$d['users'] = $users;
			} catch (UFex_Dao_NotFound $e) {
				$d['users'] = null;
			}
			
			$d['settings'] = array();
			$d['settings']['year'] = (int)$this->_srv->get('req')->get->addYear;
			$d['settings']['faculty'] = (int)$this->_srv->get('req')->get->addFaculty;
		
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function titleDormRegBookExport() {
		try {
			$bean = $this->_getDormFromGet();
			$d['dorm'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('inhabitantsNotFound');
		}
	}

	public function dormRegBookExport() {
		try {
			$bean = $this->_getDormFromGet();
			$d['dorm'] = $bean;

			try {
				$conf = UFra::shared('UFconf_Sru');
				
				$users = UFra::factory('UFbean_Sru_UserList');
				$users->listToRegBookByDorm($bean->id, $conf->usersAvailableSince);

				$d['users'] = $users;
			} catch (UFex_Dao_NotFound $e) {
				$d['users'] = null;
			}
			
			$d['settings'] = array();
			$d['settings']['year'] = (int)$this->_srv->get('req')->get->addYear;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}
	
	public function titleRoom()	{
		try {
			$bean = $this->_getRoomFromGet();
			$d['room'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}
	
	public function roomEdit() {
		try {
			$bean = $this->_getRoomFromGet();
			$d['room'] = $bean;
					
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('roomNotFound');
		}
	}
	
	/* Sprzet */
	
	public function inventory() {
		try {
			$admin = UFra::factory('UFbean_SruWalet_Admin');
			$admin->getFromSession();
			
			if ($admin->typeId != UFacl_SruWalet_Admin::HEAD) {
				try {
					$dorms = UFra::factory('UFbean_SruWalet_AdminDormitoryList');
					$dorms->listAllById($admin->id);
				} catch (UFex_Dao_NotFound $e) {
					return $this->render(__FUNCTION__.'NotFound', array());
				}
				
				$queryDorms = array();
				foreach ($dorms as $dorm) {
					$queryDorms[] = $dorm['dormitoryId'];
				}
				$bean = UFra::factory('UFbean_SruAdmin_InventoryCardList');
				$bean->listInventory($queryDorms);
				$d['inventory'] = $bean;
			} else {
				$bean = UFra::factory('UFbean_SruAdmin_InventoryCardList');
				$bean->listInventory();
				$d['inventory'] = $bean;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound', array());
		}
	}

	/* Statystyki */

	public function statsUsers() {
		try {
			$user = UFra::factory('UFbean_Sru_UserList');
			$user->listAllActive();
			$d['users'] = $user;
		
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound', $d);
		}
	}

	public function statsDormitories() {
		try {
			$user = UFra::factory('UFbean_Sru_UserList');
			$user->listAllActive();
			$d['users'] = $user;
		
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound', $d);
		}
	}

	public function statsUsersExport() {
		try {
			$user = UFra::factory('UFbean_Sru_UserList');
			$user->listAllActive();
			$d['users'] = $user;
		
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('statsUsersNotFound', $d);
		}
	}

	public function statsDormitoriesExport() {
		try {
			$user = UFra::factory('UFbean_Sru_UserList');
			$user->listAllActive();
			$d['users'] = $user;
		
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('statsDormitoriesNotFound', $d);
		}
	}

	/* Admini */

	public function admins() {
		try {
			$bean = UFra::factory('UFbean_SruWalet_AdminList');
			$bean->listAll();
			$d['admins'] = $bean;
			
			$d['dormitories'] = array();
			foreach($d['admins'] as $c){
				try{
					$admDorm = UFra::factory('UFbean_SruWalet_AdminDormitoryList');
					$admDorm->listAllById($c['id']);
					$d['dormitories'][$c['id']] = $admDorm;
				}catch(UFex_Dao_NotFound $e){
					$d['dormitories'][$c['id']] = null;	//na pewno zaden ds nie bedzie mial id null
				}
			}
			return $this->render(__FUNCTION__, $d);
		} 
		catch (UFex_Dao_NotFound $e) {
			return $this->render('adminsNotFound');
		}
	}

	public function inactiveAdmins() {
		try {
			$bean = UFra::factory('UFbean_SruWalet_AdminList');	
			$bean->listAllInactive();
			$d['admins'] = $bean;
			
			$d['dormitories'] = array();
			foreach($d['admins'] as $c){
				try{
					$admDorm = UFra::factory('UFbean_SruWalet_AdminDormitoryList');
					$admDorm->listAllById($c['id']);
					$d['dormitories'][$c['id']] = $admDorm;
				}catch(UFex_Dao_NotFound $e){
					$d['dormitories'][$c['id']] = null;	//na pewno zaden ds nie bedzie mial id null
				}
			}
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('inactiveAdminsNotFound');
		}
	}

	public function sruAdmins() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_AdminList');
			$bean->listAll();
			$d['admins'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} 
		catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'adminsNotFound');
		}
	}

	public function titleAdmin() {
		try {
			$bean = $this->_getAdminFromGet();
			$d['admin'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('titleAdminNotFound');
		}
	}

	public function admin() {
		try {
			$bean = $this->_getAdminFromGet();
			$d['admin'] = $bean;
			
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}
	
	public function adminHistory() {
		try {
			$bean = $this->_getAdminFromGet();
			$d['admin'] = $bean;
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('adminNotFound');
		}

		// uzyjemy definicji dla admina SKOSowego
		$history = UFra::factory('UFbean_SruAdmin_AdminHistoryList');
		try {
			$history->listByAdminId($bean->id);
		} catch (UFex_Dao_NotFound $e) {
		}
		$d['history'] = $history;

		return $this->render(__FUNCTION__, $d);
	}

	public function adminDorms() {
		try {
			$bean = $this->_getAdminFromGet();
			$d['admin'] = $bean;

			// kierownik osiedla nie ma przypisanych DSów
			if ($bean->typeId == UFacl_SruWalet_Admin::HEAD || $bean->typeId == UFacl_SruWalet_Admin::PORTIER) {
				return '';
			}

			try {
				$admDorm = UFra::factory('UFbean_SruWalet_AdminDormitoryList');
				$admDorm->listAllById($bean->id);
				$d['dormList'] = $admDorm;
			} catch (UFex_Dao_NotFound $e) {
				$d['dormList'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function adminDutyHours() {
		try {
			$bean = $this->_getAdminFromGet();
			$d['admin'] = $bean;

			// godziny dyżurów mają tylko admini SKOS, nawet boty ich nie mają!
			if ($bean->typeId != UFacl_SruAdmin_Admin::CENTRAL && $bean->typeId != UFacl_SruAdmin_Admin::CAMPUS && $bean->typeId != UFacl_SruAdmin_Admin::LOCAL) {
				return '';
			}

			$hours = UFra::factory('UFbean_SruAdmin_DutyHoursList');
			$hours->listByAdminId($bean->id);
			$d['hours'] = $hours;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function adminHosts() {
		try {
			$bean = $this->_getAdminFromGet();
			$d['admin'] = $bean;

			// tylko admini Waleta opiekują się hostami
			if ($bean->typeId != UFacl_SruWalet_Admin::DORM && $bean->typeId != UFacl_SruWalet_Admin::OFFICE && $bean->typeId != UFacl_SruWalet_Admin::HEAD) {
				return '';
			}

			$hosts = UFra::factory('UFbean_Sru_ComputerList');
			$hosts->listCaredByAdminId($bean->id);
			$d['hosts'] = $hosts;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function adminAdd() {
		$dorms = UFra::factory('UFbean_Sru_DormitoryList');
		$dorms->listAll();
		
		$bean = UFra::factory('UFbean_SruWalet_Admin');
		$d['admin'] = $bean;
		$d['dormitories'] = $dorms;


		return $this->render(__FUNCTION__, $d);
	}

	public function titleAdminEdit() {
		try {
			$bean = $this->_getAdminFromGet();
			$d['admin'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('titleAdminNotFound');
		}
	}

	public function adminEdit() {
		try {
			$bean = $this->_getAdminFromGet();
			$dorms = UFra::factory('UFbean_Sru_DormitoryList');
			$dorms->listAll();

			try {
				$admDorm = UFra::factory('UFbean_SruWalet_AdminDormitoryList');
				$admDorm->listAllById($bean->id);
				$d['dormList'] = $admDorm;
			} catch (UFex_Dao_NotFound $e) {
				$d['dormList'] = null;
			}
			
			$bean = $this->_getAdminFromGet();
			$acl  = $this->_srv->get('acl');
	
			$d['admin'] = $bean;
			$d['dormitories'] = $dorms;
			$d['advanced'] = $acl->sruWalet('admin', 'advancedEdit');

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('adminNotFound');
		}
	}

	public function adminUsersModified() {
		try{
			$bean = $this->_getAdminFromGet();
			
			$modified = UFra::factory('UFbean_Sru_UserList');
			$modified->listLastModified($bean->id);
			$d['modifiedUsers'] = $modified;
			
			return $this->render(__FUNCTION__, $d);
		}catch(UFex_Dao_NotFound $e){
			return '';
		}	
	}
	
	public function ownPswEdit(){
	    try{
		$bean = UFra::factory('UFbean_SruWalet_Admin');
		$bean->getFromSession();
		
		$d['admin'] = $bean;
		
		return $this->render(__FUNCTION__, $d);
	    } catch (UFex_Dao_NotFound $e) {
		return  $this->render('adminNotFound');
	    }
	}
}
