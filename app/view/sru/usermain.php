<?
/**
 * glowny panel uzytkownika
 */
class UFview_Sru_UserMain
extends UFview_SruUser {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleMain());
		$this->append('body', $box->userMainMenu());
		$this->append('body', $box->userInfo());
	}
}
