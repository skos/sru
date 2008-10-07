<?
/**
 * konfig dhcp (komputery administracji)
 */
class UFview_SruApi_DhcpAdm
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->dhcpAdm());
	}
}
