<?
/**
 * uzytkownicy do calkowitego usuniecia
 */
class UFview_SruApi_UsersToRemove
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->usersToRemove());
	}
}
