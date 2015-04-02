<?php
/**
 * logi
 */
class UFbox_Log
extends UFbox {
	
	public function full() {
		$bean = UFra::factory('UFbean_Core_LogList');
		$bean->listAll();

		$d['logs'] = $bean;

		return $this->render('full', $d);
	}
}
