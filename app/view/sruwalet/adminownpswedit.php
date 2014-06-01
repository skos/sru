<?php
/**
 * edycja hasła
 */
class UFview_SruWalet_AdminOwnPswEdit
extends UFview_SruWalet {

	public function fillData() {
	    $box  = UFra::shared('UFbox_SruWalet');

	    $this->append('title', $box->titleOwnPswEdit());
	    $this->append('body',  $box->ownPswEdit());
	}
}