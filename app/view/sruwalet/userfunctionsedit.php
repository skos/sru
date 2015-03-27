<?
/**
 * edycja funkcji uzytkownika
 */
class UFview_SruWalet_UserFunctionsEdit
extends UFview_SruWalet {

	public function fillData() {
		$box = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleUserFunctionsEdit());
		$this->append('body', $box->user());
		$this->append('body', $box->userFunctionsEdit());
	}
}
