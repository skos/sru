<?
/**
 * ponowna aktywacja wlasnego komputera
 */
class UFview_Sru_UserComputerActivate
extends UFview_SruUser {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleUserComputer());
		$this->append('body', $box->userComputerEdit(true));
	}
}
