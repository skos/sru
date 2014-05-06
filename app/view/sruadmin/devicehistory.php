<?php
class UFview_SruAdmin_DeviceHistory
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleDevice());
		$this->append('body', $box->deviceDetails());
		$this->append('body', $box->deviceHistory());
	}
}
