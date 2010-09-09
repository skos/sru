<?
/**
 * logowanie do systemu
 */
class UFview_SruWalet_Login
extends UFview {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleLogin());
		$this->append('body', $box->login());
	}
}
