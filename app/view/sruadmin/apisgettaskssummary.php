<?php
class UFview_SruAdmin_ApisGetTasksSummary
extends UFview_SruApi {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('body', $box->apisGetTasksSummary());
	}
}
