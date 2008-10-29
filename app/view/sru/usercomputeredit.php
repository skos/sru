<?
/**
 * edycja wlasnego komputera
 */
class UFview_Sru_UserComputerEdit
extends UFview_SruUser {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleUserComputer());
		$this->append('body', $box->userComputerEdit());
		$this->append('body', $box->userComputerDel());
	}
}
