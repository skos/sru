<?
/**
 * konfig dns domeny ds.pg.gda.pl
 */
class UFview_SruApi_Dns
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->dns());
	}
}
