<?
/**
 * lista komputerow uzytkownika
 */
class UFview_Sru_UserComputers
extends UFview {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleUserComputers());
		$this->append('body', $box->userComputers());
	}
}
