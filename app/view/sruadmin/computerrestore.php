<?
/**
 * przywrocenie danych komputera
 */
class UFview_SruAdmin_ComputerRestore
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleComputerEdit());
		$this->append('body', $box->computer());
		$this->append('body', $box->computerEdit());
	}
}
