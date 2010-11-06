<?
/**
 * wyniki wyszukiwania komputerow
 */
class UFview_SruAdmin_ComputerSearchResults
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleComputerSearch());
		$this->append('body', $box->computerSearch());
		$this->append('body', $box->computerSearchResults());
		$this->append('body', $box->computerSearchByAliasResults());
	}
}
