<?
/**
 * konfig rev-dns
 */
class UFview_SruApi_DnsRev
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->dnsRev());
	}
}
