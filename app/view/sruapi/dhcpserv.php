<?
/**
 * konfig dhcp (serwery)
 */
class UFview_SruApi_DhcpServ
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->dhcpServ());
	}
}
