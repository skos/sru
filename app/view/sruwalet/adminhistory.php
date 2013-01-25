<?
/**
 * historyczne dane admina
 */
class UFview_SruWalet_AdminHistory
extends UFview_SruWalet {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleAdmin());
		$this->append('body', $box->admin());
		$this->append('body', $box->adminHistory());
	}
}
