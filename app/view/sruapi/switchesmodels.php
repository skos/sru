<?
/**
 * lista ethers
 */
class UFview_SruApi_SwitchesModels
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->switchesModels());
	}
}
