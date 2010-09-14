<?
/**
 * dodanie uzytkownika
 */
class UFview_SruWalet_UserAdd
extends UFview_SruWalet {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleUserAdd());
		$this->append('body', $box->userAdd());
	}
}
