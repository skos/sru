<?
/**
 * konfig dhcp (serwery)
 */
class UFview_SruApi_DhcpSrv
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->dhcpSrv());
	}
}
