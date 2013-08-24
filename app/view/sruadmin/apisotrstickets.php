<?php
class UFview_SruAdmin_ApisOtrsTickets
extends UFview_SruApi {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('body', $box->apisOtrsTickets());
	}
}
