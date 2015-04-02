<?
/**
 * strona z info, ze wszystko poszlo ok
 */
class UFview_SruApi_Status200
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->status200());
	}
}
