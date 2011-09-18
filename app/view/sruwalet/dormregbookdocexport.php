<?
/**
 * widok eksportu
 */
class UFview_SruWalet_DormRegBookDocExport
extends UFview_SruDocExport {

	public function fillData() {
		$box = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleDormRegBookExport());
		$this->append('body', $box->dormRegBookExport());
	}
}
