<?php
abstract class UFview
extends UFlib_ClassWithService {
	
	/**
	 * template 
	 */
	protected $tpl;

	/**
	 * domyslny layout dla tego widoku
	 */
	protected $layout = 'index';

	/**
	 * dane, ktorymi wypelniany jest szablon
	 */
	protected $data = array();

	public function __construct(UFlib_Services &$srv=null) {
		parent::__construct($srv);
		$this->tpl = $this->chooseTemplate();
	}

	/**
	 * wybiera template obslugujacy widok
	 * @return UFtpl_*
	 */
	protected function chooseTemplate() {
		return UFra::factory('UFtpl_Index', $this->_srv);
	}

	/**
	 * dokleja dane
	 * 
	 * @param string $target - do jakiej zmiennej dopisac?
	 * @param string $data - co dopisac?
	 * @return bool - czy istniala juz zmienna?
	 */
	protected function append($target, $data) {
		if (!is_string($target)) {
			throw UFra::factory('UFex_Core_NoParameter', 'target');
		}
		if (!is_string($data)) {
			throw UFra::factory('UFex_Core_NoParameter', 'data');
		}
		if (isset($this->data[$target])) {
			$this->data[$target] .= $data;
			return true;
		} else {
			$this->data[$target] = $data;
			return false;
		}
	}

	/**
	 * wypelnia dane
	 */
	abstract protected function fillData();

	/**
	 * wypelnia domyslne dane
	 */
	protected function fillDefaultData() {
	}

	/**
	 * generuje tresc przesylana do klienta
	 */
	public function go() {
		$this->fillData();
		$this->fillDefaultData();
		ob_start();
		$this->tpl->{$this->layout}($this->data);
		$out = ob_get_contents();
		ob_end_clean();

		echo $out;
	}
}
