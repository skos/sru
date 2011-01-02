<?
/**
 * wyświetlenie dla usera jego uploadu
 */
class UFview_SruApi_MyLanstats
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->myLanstats());
	}
}
