<?php
abstract class UFex
extends Exception {
	
	protected $level;
	protected $data;
	
	public function getData() {
		return $this->data;
	}

	public function __construct($message = null, $code = 0, $level = E_NOTICE, $data=null) {
		if (!is_int($code)) {
			$code = 0;
		}
		if (!is_int($level)) {
			$level = E_NOTICE;
		}
		parent::__construct($message, $code);
		$this->level = $level;
		if (!is_null($data)) {
			$this->data = $data;
		}
	}
}
