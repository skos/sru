<?php
class UFview_SruAdmin_SwitchData
extends UFview_SruApi {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('body', $box->switchData());
	}
}
