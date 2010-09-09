<?
/**
 * historia uzytkownika
 */
class UFview_SruWalet_UserHistory
extends UFview_SruWalet {

	public function fillData() {
		$box = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleUser());
		$this->append('body', $box->user());
		$this->append('body', $box->userHistory());
	}
}
