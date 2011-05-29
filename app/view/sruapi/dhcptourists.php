<?
/**
 * konfig dhcp (komputery turystów)
 */
class UFview_SruApi_DhcpTourists
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->dhcpTourists());
	}
}
