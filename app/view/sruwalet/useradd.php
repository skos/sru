<?
/**
 * dodanie uzytkownika
 */
class UFview_SruWalet_UserAdd
extends UFview {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleUserAdd());
		$this->append('body', $box->userAdd(true));
	}
}
