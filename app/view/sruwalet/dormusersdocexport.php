<?
/**
 * widok eksportu
 */
class UFview_SruWalet_DormUsersDocExport
extends UFview_SruDocExport {

	public function fillData() {
		$box = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleDormUsersExport());
		$this->append('body', $box->dormUsersExport());
	}
}
