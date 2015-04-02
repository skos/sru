<?php
class UFview_SruAdmin_FwExceptionEdit
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleFwExceptionEdit());
		$this->append('body', $box->fwExceptionEdit());
	}
}
