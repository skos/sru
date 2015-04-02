<?
/**
 * edycja wyjatkow FW komputera
 */
class UFview_SruAdmin_ComputerFwExceptionsEdit
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleComputerFwExceptionsEdit());
		$this->append('body', $box->computer());
		$this->append('body', $box->computerFwExceptionsEdit());
	}
}
