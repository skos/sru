<?php
class UFview_Sru_UserPenalties
extends UFview_SruUser {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titlePenalties());
		$this->append('body', $box->userPenalties());
		

	}
}
