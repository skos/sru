<?
/**
 * strona z bledem 404 w api
 */
class UFview_SruApi_Error404
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->error404());
	}
}
