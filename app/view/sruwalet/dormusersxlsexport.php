<?
/**
 * widok eksportu
 */
class UFview_SruWalet_DormUsersXlsExport
extends UFview_SruXlsExport {

	public function fillData() {
		$box = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleDormUsersExport());
		$this->append('body', $box->dormUsersExport());
	}
}
