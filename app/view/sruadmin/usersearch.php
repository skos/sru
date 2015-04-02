<?
/**
 * wyszukiwanie uzytkownika
 */
class UFview_SruAdmin_UserSearch
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleUserSearch());
		$this->append('body', $box->userSearch());
	}
}
