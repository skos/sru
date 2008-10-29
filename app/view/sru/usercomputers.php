<?
/**
 * lista komputerow uzytkownika
 */
class UFview_Sru_UserComputers
extends UFview_SruUser {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleUserComputers());
		$this->append('body', $box->userComputers());
	}
}
