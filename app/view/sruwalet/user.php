<?
/**
 * dane uzytkownika
 */
class UFview_SruWalet_User
extends UFview_SruWalet {

	public function fillData() {
		$box = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleUser());
		$this->append('body', $box->user());
	}
}
