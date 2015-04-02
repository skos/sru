<?php
/**
 * zmiana portów switcha
 */
class UFview_SruAdmin_SwitchPortsEdit
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleSwitchPortsEdit());
		$this->append('body',  $box->switchPortsEdit());
	}
}
