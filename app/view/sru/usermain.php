<?
/**
 * glowny panel uzytkownika
 */
class UFview_Sru_UserMain
extends UFview {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleMain());
		$this->append('body', $box->userMainMenu());
		$this->append('body', $box->logout());
	}
}
