<?php
class UFview_SruAdmin_SwitchInventoryCardHistory
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleSwitch());
		$this->append('body', $box->switchDetails());
		$this->append('body', $box->inventoryCardHistory());
	}
}
