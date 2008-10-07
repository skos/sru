<?
/**
 * konfig dns domeny adm.ds.pg.gda.pl
 */
class UFview_SruApi_DnsAdm
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->dnsAdm());
	}
}
