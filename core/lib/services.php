<?php
/**
 * uslugi systemowe
 */
class UFlib_Services {
	
	/**
	 * lista zarejestrowanych uslug
	 */
	protected $services = array();
	
	/**
	 * dodaje nowa usluge
	 *
	 * @param string $service - nazwa uslugi
	 * @param mixed $ref - referencja do uslugi
	 * @throws Ex_Core_ServiceExists - usluga juz istnieje
	 */
	function set($service,&$ref) {
		if (isset($this->services[$service])) {
			throw new Ex_Core_ServiceExists('Service "'.$service.'" exists');
		}
		$this->services[$service] =& $ref;
	}

	/**
	 * zwraca usluge
	 *
	 * @param string $service - nazwa uslugi
	 * @return mixed - referencja do uslugi
	 * @throws UFex_Core_ServiceNotFound - usluga nie istnieje
	 */
	function &get($service) {
		$service = (string)$service;
		if (!isset($this->services[$service])) {	// referencji nie ma w tablicy
			throw new UFex_Core_ServiceNotFound('Service "'.$service.'" not found', 0, E_WARNING);
		}
		return $this->services[$service];
	}
}
