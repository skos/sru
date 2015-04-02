<?php
/**
 * zmiana portu switcha
 */
class UFview_SruAdmin_SwitchPortEdit
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleSwitchEdit());
		$this->append('body', $box->switchDetails());
		$this->append('body', $box->switchPorts());
		$this->append('body', $box->switchPortEdit());
	}
}
