<?
/**
 * dodanie admina Waleta
 */
class UFview_SruWalet_AdminAdd
extends UFview_SruWalet {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleAdminAdd());
		$this->append('body',  $box->adminAdd());
	}
}
