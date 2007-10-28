<?
/**
 * komputer uzytkownika
 */
class UFview_Sru_UserComputer
extends UFview {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleUserComputer());
		$this->append('body', $box->userComputer());
	}
}
