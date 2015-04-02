<?
/**
 * lista ethers
 */
class UFview_SruApi_SwitchesModelIps
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->switchesModelIps());
	}
}
