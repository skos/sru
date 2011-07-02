<?
/**
 * dane uzytkownika
 */
class UFview_SruAdmin_User
extends UFview_SruAdmin {

	public function fillData() {
		$box = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleUser());
		$this->append('body', $box->user());
		$this->append('body', $box->userComputers());
		$this->append('body', $box->roomSwitchPorts());
		$this->append('body', $box->userInactiveComputers());
		$this->append('body', $box->userServicesEdit());
	}
}
