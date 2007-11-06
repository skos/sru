<?
/**
 * logowanie do systemu
 */
class UFview_SruAdmin_Main
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleComputers());
		$this->append('body', $box->computers());
	}
}
