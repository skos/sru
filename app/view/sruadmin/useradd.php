<?
/**
 * dodanie uzytkownika
 */
class UFview_SruAdmin_UserAdd
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleUserAdd());
		$this->append('body', $box->userAdd());
	}
}
