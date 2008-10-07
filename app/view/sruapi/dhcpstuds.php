<?
/**
 * konfig dhcp (komputery studenckie)
 */
class UFview_SruApi_DhcpStuds
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->dhcpStuds());
	}
}
