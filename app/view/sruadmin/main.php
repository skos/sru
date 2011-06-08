<?
/**
 * logowanie do systemu
 */
class UFview_SruAdmin_Main
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleMain());
		$this->append('body', $box->userSearch());
		$this->append('body', $box->computerSearch());
		$this->append('body', $box->toDoList());
	}
}
