<?php
class UFview_SruAdmin_SwitchInventoryCardEdit
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleInventoryCardEdit());
		$this->append('body', $box->switchDetails());
		$this->append('body', $box->inventoryCardEdit());
	}
}
