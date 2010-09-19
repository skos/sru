<?
/**
 * szybkie wyszukiwanie
 */
class UFview_SruWalet_UserQuickSearch
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruWalet');

		$this->append('body', $box->quickUserSearchResults());
	}
}

