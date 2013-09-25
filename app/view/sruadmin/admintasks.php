<?
/**
 * logowanie do systemu
 */
class UFview_SruAdmin_AdminTasks
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleTasks());
		$this->append('body', $box->toDoList());
	}
}
