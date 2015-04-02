<?
/**
 * edycja aliasów komputera
 */
class UFview_SruAdmin_ComputerAliasesEdit
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleComputerAliasesEdit());
		$this->append('body', $box->computer());
		$this->append('body', $box->computerAliasesEdit());
	}
}
