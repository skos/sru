<?
/**
 * lista ethers
 */
class UFview_SruApi_FirewallExceptions
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->firewallExceptions());
	}
}
