<?
/**
 * dodanie komputera
 */
class UFview_SruAdmin_ComputerAdd
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');
		$this->append('title', $box->titleComputerAdd());
		
		$this->append('body', $box->user());
		$this->append('body', $box->computerAdd());
		$this->append('body', $box->userComputers());
		$this->append('body', $box->userInactiveComputers());
	}
}
