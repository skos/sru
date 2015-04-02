<?php
class UFview_SruAdmin_FwExceptionList
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleFwExceptions());
		$this->append('body', $box->fwExceptions());
	}
}
