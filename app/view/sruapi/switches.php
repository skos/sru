<?
/**
 * lista IP switchy
 */
class UFview_SruApi_Switches
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->switches());
	}
}
