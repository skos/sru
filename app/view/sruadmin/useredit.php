<?
/**
 * dane uzytkownika
 */
class UFview_SruAdmin_UserEdit
extends UFview_SruAdmin {

	public function fillData() {
		$box = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleUserEdit());
		$this->append('body', $box->user());
		$this->append('body', $box->userEdit());
	}
}
