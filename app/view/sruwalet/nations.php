<?php
class UFview_SruWalet_Nations
extends UFview_SruWalet {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleNations());
		$this->append('body', $box->nations());
	}
}
