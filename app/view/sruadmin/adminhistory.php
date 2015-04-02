<?
/**
 * historyczne dane admina
 */
class UFview_SruAdmin_AdminHistory
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleAdmin());
		$this->append('body', $box->admin());
		$this->append('body', $box->adminHistory());
	}
}
