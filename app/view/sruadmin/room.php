<?php
/**
 * dane pokoju
 */
class UFview_SruAdmin_Room
extends UFview_SruAdmin {

	public function fillData() {
		$box = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleRoom());
		$this->append('body',  $box->room());
		$this->append('body',  $box->roomUsers());
		$this->append('body',  $box->roomComputers());
		$this->append('body',  $box->roomDevices());
		$this->append('body',  $box->roomSwitches());
		$this->append('body',  $box->roomSwitchPorts());

	}
}
