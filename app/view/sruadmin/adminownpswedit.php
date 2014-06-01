<?php
/**
 * edycja hasła
 */
class UFview_SruAdmin_AdminOwnPswEdit
extends UFview_SruAdmin {

	public function fillData() {
	    $box  = UFra::shared('UFbox_SruAdmin');

	    $this->append('title', $box->titleOwnPswEdit());
	    $this->append('body',  $box->ownPswEdit());
	}
}
