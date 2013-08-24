<?
/**
 * uzytkownik niezarejestrowany
 */
class UFview_Sru_UserUnregistered
extends UFview {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleUserUnregistered());
		$this->append('body', $box->userUnregistered());
		$this->append('body', $box->login());
		$this->append('body', $box->recoverPassword());
		$this->append('body', $box->userUnregisteredMore());
	}
}
