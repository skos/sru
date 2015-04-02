<?
/**
 * zalozenie admina
 */
class UFview_SruAdmin_AdminAdd
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleAdminAdd());
		$this->append('body',  $box->adminAdd());
	}
}
