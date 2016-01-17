<?
/**
 * lista wyjatkow do wylaczenia
 */
class UFview_SruApi_FirewallExceptionsOutdated
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->firewallExceptionsOutdated());
	}
}
