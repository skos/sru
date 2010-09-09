<?
/**
 * dane uzytkownika
 */
class UFview_SruWalet_UserEdit
extends UFview_SruWalet {

	public function fillData() {
		$box = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleUserEdit());
		$this->append('body', $box->user());
		$this->append('body', $box->userEdit());
	}
}
