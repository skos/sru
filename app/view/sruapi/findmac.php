<?
/**
 * find-mac
 */
class UFview_SruApi_FindMac
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->findMac());
	}
}
