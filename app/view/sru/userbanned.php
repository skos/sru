<?
/**
 * uzytkownik zbanowany
 */
class UFview_Sru_UserBanned
extends UFview {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleUserBanned());
		$this->append('body', $box->userBanned());
		$this->append('body', $box->login());
		$this->append('body', $box->recoverPassword());
	}
}
