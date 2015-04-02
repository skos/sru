<?
/**
 * główny widok
 */
class UFview_SruWalet_Main
extends UFview_SruWalet {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruWalet');   
		$this->append('title', $box->titleMain());       
		$this->append('body', $box->userSearch());        
		$this->append('body', $box->toDoList());
		$this->append('body', $box->mainPageInfo());
	}
}
