<?
/**
 * uzytkownicy oznaczeni do wymeldowania
 */
class UFview_SruApi_UsersToDeactivate
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->usersToDeactivate());
	}
}
