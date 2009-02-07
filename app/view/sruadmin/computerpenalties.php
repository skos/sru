<?php
class UFview_SruAdmin_ComputerPenalties
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleComputerPenalties());
		$this->append('body', $box->computerPenalties());
		

	}
}
