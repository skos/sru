<?
/**
 * dodanie komputera
 */
class UFview_Sru_UserComputerAdd
extends UFview_SruUser {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleUserComputerAdd());
		$this->append('body', $box->userComputerAdd());
	}
}
