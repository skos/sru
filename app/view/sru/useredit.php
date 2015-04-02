<?
/**
 * edycja profilu przez uzytkownika
 */
class UFview_Sru_UserEdit
extends UFview_SruUser {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleUserEdit());
		$this->append('body', $box->userEdit());
	}
}
