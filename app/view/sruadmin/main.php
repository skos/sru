<?
/**
 * logowanie do systemu
 */
class UFview_SruAdmin_Main
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleMain());
		$this->append('body', $box->leftColumnStart());
		$this->append('body', $box->userSearch());
		$this->append('body', $box->computerSearch());
		$this->append('body', $box->inventoryCardSearch());
		$this->append('body', $box->columnEnd());
		$this->append('body', $box->rightColumnStart());
		$this->append('body', $box->tasksSummary());
		$this->append('body', $box->adminLists());
		$this->append('body', $box->columnEnd());
	}
}
