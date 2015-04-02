<?
/**
 * historia uzytkownika
 */
class UFview_SruAdmin_UserHistory
extends UFview_SruAdmin {

	public function fillData() {
		$box = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleUser());
		$this->append('body', $box->user());
		$this->append('body', $box->userHistory());
	}
}
