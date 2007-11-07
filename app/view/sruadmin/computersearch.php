<?
/**
 * wyszukiwanie komputerow
 */
class UFview_SruAdmin_ComputerSearch
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleComputerSearch());
		$this->append('body', $box->computerSearch());
	}
}
