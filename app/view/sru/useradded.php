<?
/**
 * zalozono uzytkownika
 */
class UFview_Sru_UserAdded
extends UFview {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleUserAdded());
		$this->append('body', $box->userAdded());
	}
}
