<?php
class UFview_SruWalet_StatsDormitoriesDocExport
extends UFview_SruDocExport {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleStatsDormitoriesExport());
		$this->append('body', $box->statsDormitoriesExport());
	}
}
