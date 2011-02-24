<?
/**
 * admini, ktorym skonczyla sie rejestracja
 */
class UFview_SruApi_AdminsOutdated
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->adminsOutdated());
	}
}
