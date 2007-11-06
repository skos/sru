<?
/**
 * logowanie do systemu
 */
class UFview_SruAdmin_Login
extends UFview {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleLogin());
		$this->append('body', $box->login());
	}
}
