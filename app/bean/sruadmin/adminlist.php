<?php
/**
 * admini
 */
class UFbean_SruAdmin_AdminList
extends UFbeanList {
	
	public function deactivateOutdated($user){
		return $this->dao->deactivateOutdated($user);
	}
}