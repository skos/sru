<?php
class UFview_SruAdmin_DeviceInventoryCardEdit
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleInventoryCardEdit());
		$this->append('body', $box->deviceDetails());
		$this->append('body', $box->inventoryCardEdit());
	}
}
