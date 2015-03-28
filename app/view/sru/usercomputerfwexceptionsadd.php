<?
/**
 * dodawanie wyjatkow fw
 */
class UFview_Sru_UserComputerFwExceptionsAdd
extends UFview_SruUser {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleUserComputer());
		$this->append('body', $box->userComputerFwExceptionsAdd());
	}
}
