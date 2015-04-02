<?php
/**
 * klasa, ktora musi posiadac uslugi
 */
abstract class UFlib_ClassWithService {

	/**
	 * uslugi
	 */
	protected $_srv;
	
	/**
	 * tworzy lokalna referencje do uslug
	 * 
	 * @param mixed $srv - klasa uslug
	 */
	public function __construct(&$srv=null) {
		if (!is_object($srv) || !($srv instanceof UFlib_Services)) {
			$srv =& UFra::services();
		}
		$this->_srv =& $srv;
	}
}
