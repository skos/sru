<?
/**
 * dodanie urzadzenia
 */
class UFview_SruAdmin_DeviceAdd
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleDeviceAdd());
		$this->append('body',  $box->deviceAdd());
	}
}
