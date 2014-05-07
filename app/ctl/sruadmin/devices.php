<?php
/**
 * front controller czesci administracyjnej sru modulu urzadzen
 */
class UFctl_SruAdmin_Devices
extends UFctl {

	protected function parseParameters() {
		$req = $this->_srv->get('req');
		$get = $req->get;
		
		$segCount = $req->segmentsCount();

		// wyzsze segmenty sprawdzane sa w front'ie
		if (1 == $segCount) {
			$get->view = 'devices/main';
		} else {
			switch ($req->segment(2)) {
				case ':add':
					$get->view = 'devices/add';
					break;
				case 'dorm':
					if (2 == $segCount) {
						$get->view = 'error404';
					} else {
						switch ($req->segment(3)) {
							default:
								$get->view = 'devices/main';
								$id = $req->segment(3);
								$get->dormAlias = $id;
						}
					}
					break;
				default:
					$get->view = 'devices/device';
					$id = (int)$req->segment(2);
					if ($id <= 0) {
						$get->view = 'error404';
						break;
					}
					$get->deviceId = $id;

					if ($segCount > 2) {
						switch ($req->segment(3)) {
							case ':edit':
								$get->view = 'devices/edit';
								break;
							case 'history':
								$get->view = 'devices/history';
								break;
							case 'inventorycardhistory':
								$get->view = 'inventorycard/history';
								break;
							case ':inventorycardadd':
								$get->view = 'inventorycard/add';
								break;
							case ':inventorycardedit':
								$get->view = 'inventorycard/edit';
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
		} elseif ('devices/add' == $get->view && $post->is('deviceAdd') && $acl->sruAdmin('device', 'add')) {
			$act = 'Device_Add';
		} elseif ('devices/edit' == $get->view && $post->is('deviceEdit') && $acl->sruAdmin('device', 'edit')) {
			$act = 'Device_Edit';
		} elseif ('inventorycard/add' == $get->view && $post->is('inventoryCardAdd') && $acl->sruAdmin('device', 'inventoryCardAdd')) {
			$act = 'InventoryCard_Add';
		} elseif ('inventorycard/edit' == $get->view && $post->is('inventoryCardEdit') && $acl->sruAdmin('device', 'inventoryCardEdit')) {
			$act = 'InventoryCard_Edit';
		}

		if (isset($act)) {
			$action = 'SruAdmin_'.$act;
		}

		return $action;
	}

	protected function chooseView($view = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;
		$msg = $this->_srv->get('msg');
		$acl = $this->_srv->get('acl');

		if (!$acl->sruAdmin('admin', 'logout')) {
			return 'SruAdmin_Login';
		}
		
		switch ($get->view) {
			case 'devices/main':
				return 'SruAdmin_Devices';
			case 'devices/device':
				return 'SruAdmin_Device';
			case 'devices/add':
				if ($msg->get('deviceAdd/ok')) {
					return 'SruAdmin_Devices';
				} elseif ($acl->sruAdmin('device', 'add')) {
					return 'SruAdmin_DeviceAdd';
				} else {
					return 'Sru_Error404';
				}
			case 'devices/edit':
				if ($msg->get('deviceEdit/ok')) { 
					return 'SruAdmin_Device';
				} elseif ($acl->sruAdmin('device', 'edit')) {
					return 'SruAdmin_DeviceEdit';
				} else {
					return 'Sru_Error403';
				}
			case 'devices/history':
				return 'SruAdmin_DeviceHistory';
			case 'inventorycard/history':
				if ($acl->sruAdmin('device', 'inventoryCardView')) {
					return 'SruAdmin_DeviceInventoryCardHistory';
				} else {
					return 'Sru_Error403';
				}
			case 'inventorycard/add':
				if ($msg->get('inventoryCardAdd/ok')) { 
					return 'SruAdmin_Device';
				} elseif ($acl->sruAdmin('device', 'inventoryCardAdd')) {
					return 'SruAdmin_DeviceInventoryCardAdd';
				} else {
					return 'Sru_Error403';
				}
			case 'inventorycard/edit':
				if ($msg->get('inventoryCardEdit/ok')) { 
					return 'SruAdmin_Device';
				} elseif ($acl->sruAdmin('device', 'inventoryCardEdit')) {
					return 'SruAdmin_DeviceInventoryCardEdit';
				} else {
					return 'Sru_Error403';
				}
			default:
				return 'Sru_Error404';
		}
	}
}
