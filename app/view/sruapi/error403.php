<?
/**
 * strona z bledem 403 w api
 */
class UFview_SruApi_Error403
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->error403());
	}
}
