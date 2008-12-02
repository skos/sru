<?
/**
 * logowanie do systemu
 */
class UFview_Sru_Login
extends UFview {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleLogin());
		$this->append('body', $box->login());
		$this->append('body', $box->recoverPassword());
	}
}
