<?php
class UFlib_LogWrapper {
	
	/**
	 * przechowuje opakowana klase
	 */
	protected $__class = null;

	/**
	 * logger wlasciwy
	 */
	protected $__logger = null;

	protected $__className;
	
	function __construct(&$class, $logger) {
		if (!is_object($class)) {
			throw new Ex_Core_NoParameter('Parameter "class" not found.');
		}
		if (!is_object($class) || !($logger instanceof Lib_Log)) {
			throw new Ex_Core_NoParameter('Parameter "logger" not found.');
		}
		$this->__className = get_class($class);
		$this->__class  =& $class;
		$this->__logger =& $logger;
	}

	function __call($func, $params) {
		$this->__logger->debug($this->__className.'->'.$func.' params', $params);
		$return = call_user_func_array(array($this->__class, $func), $params);
		$this->__logger->debug($this->__className.'->'.$func.' result', $return);
		return $return;
	}

	function __set($param, $val) {
		$this->__class->{$param} = $val;
	}

	function __get($param) {
		return $this->__class->{$param};
	}

	/**
	 * "odpakowuje" klase
	 */
	function &__unwrap() {
		return $this->__class;
	}
}
