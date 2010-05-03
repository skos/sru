<?
/**
 * zalozenie switcha
 */
class UFview_SruAdmin_SwitchAdd
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleSwitchAdd());
		$this->append('body',  $box->switchAdd());
	}
}
