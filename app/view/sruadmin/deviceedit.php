<?php
/**
 * zmiana urzadzenia
 */
class UFview_SruAdmin_DeviceEdit
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleDeviceEdit());
		$this->append('body',  $box->deviceEdit());
	}
}
