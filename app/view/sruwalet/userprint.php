<?
/**
 * dane wydruku
 */
class UFview_SruWalet_UserPrint
extends UFview_SruPrint {

	public function fillData() {
		$box = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleUserPrint());
		$this->append('body', $box->userPrint());
	}
}
