<?
/**
 * konfig dhcp (komputery organizacji)
 */
class UFview_SruApi_DhcpOrg
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->dhcpOrg());
	}
}
