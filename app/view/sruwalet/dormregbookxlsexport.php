<?
/**
 * widok eksportu
 */
class UFview_SruWalet_DormRegBookXlsExport
extends UFview_SruXlsExport {

	public function fillData() {
		$box = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleDormRegBookExport());
		$this->append('body', $box->dormRegBookExport());
	}
}
