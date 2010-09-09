<?
/**
 * wyszukiwanie uzytkownika
 */
class UFview_SruWalet_UserSearchResults
extends UFview_SruWalet {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleUserSearch());
		$this->append('body', $box->userSearch());
		$this->append('body', $box->userSearchResults());
	}
}
