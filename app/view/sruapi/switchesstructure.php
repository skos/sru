<?
/**
 * lista struktury switchy
 */
class UFview_SruApi_SwitchesStructure
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->switchesStructure());
	}
}
