<?
/**
 * lista ethers
 */
class UFview_SruApi_ExAdmins
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->exadmins());
	}
}
