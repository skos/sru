<?
/**
 * konfig dhcp
 */
class UFview_SruApi_Dhcp
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->dhcp());
	}
}
