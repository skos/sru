<?php
/**
 * front controller czesci administracyjnej sru dotyczacej wyposażenia
 */
class UFctl_SruAdmin_Inventory
extends UFctl {

	protected function parseParameters() {
		$req = $this->_srv->get('req');
		$get = $req->get;
		
		$segCount = $req->segmentsCount();

		// wyzsze segmenty sprawdzane sa w front'ie
		if (1 == $segCount) {
			$get->view = 'inventory/main';
		} else {
			switch ($req->segment(2)) {
				case 'search':
					$get->view = 'inventory/search';
					for ($i=3; $i<=$segCount; ++$i) {
						
						$tmp = explode(':', $req->segment($i), 2);
						switch ($tmp[0]) {
							case 'serialNo':
								$get->searchedSerialNo = str_replace('\\', '/', urldecode($tmp[1]));
								break;
							case 'inventoryNo':
								$get->searchedInventoryNo = urldecode($tmp[1]);
								break;
							case 'dormitory':
								$get->searchedDormitory = urldecode($tmp[1]);
								break;
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
		$post = $req->post;
		$acl = $this->_srv->get('acl');

		if ($post->is('adminLogout') && $acl->sruAdmin('admin', 'logout')) {
			$act = 'Admin_Logout';
		} elseif ($post->is('adminLogin') && $acl->sruAdmin('admin', 'login')) {
			$act = 'Admin_Login';
		} elseif ($post->is('inventoryCardSearch')) {
			$act = 'InventoryCard_Search';
		}
		
		if (isset($act)) {
			$action = 'SruAdmin_'.$act;
		}

		return $action;
	}

	protected function chooseView($view = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$acl = $this->_srv->get('acl');

		if (!$acl->sruAdmin('admin', 'logout')) {
			return 'SruAdmin_Login';
		}
		
		switch ($get->view) {
			case 'inventory/main':
				return 'SruAdmin_Inventory';
			case 'inventory/search':
				return 'SruAdmin_InventoryCardSearchResults';
			default:
				return 'Sru_Error404';
		}
	}
}
