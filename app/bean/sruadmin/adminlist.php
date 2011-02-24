<?php
/**
 * admini
 */
class UFbean_SruAdmin_AdminList
extends UFbeanList {
	
	public function deactivateOutdated(){
		return $this->dao->deactivateOutdated();
	}
}