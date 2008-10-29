<?
/**
 * komputer uzytkownika
 */
class UFview_Sru_UserComputer
extends UFview_SruUser {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleUserComputer());
		$this->append('body', $box->userComputer());
	}
}
