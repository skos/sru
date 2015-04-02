<?php
/**
 * reprezentacja danych
 */
abstract class UFbean
extends UFlib_ClassWithService {
	
	const SOURCE_DAO = 1;	// dane pochodza z dao

	/**
	 * dane
	 */
	protected $data = array();

	/**
	 * klasa dostepu do danych
	 */
	protected $dao = null;

	/**
	 * template 
	 */
	protected $tpl;

	/**
	 * zrodlo pochodzenia aktualnie przechowywanych danych
	 */
	protected $source;

	public function __construct(Lib_Service &$srv = null) {
		parent::__construct($srv);
		$this->dao = $this->chooseDao();
		$this->tpl = $this->chooseTemplate();
	} 

	/**
	 * wybiera obiekt dostarczajacy template'y
	 */
	protected function chooseTemplate() {
		$className = get_class($this);
		$className = 'UFtpl'.stristr($className, '_');
		$className = preg_replace('/List$/', '', $className);
		return UFra::shared($className, true);
	}

	/**
	 * wybiera obiekt dostepu do danych
	 */
	protected function chooseDao() {
		$className = get_class($this);
		$className = 'UFdao'.stristr($className, '_');
		$className = preg_replace('/List$/', '', $className);
		return UFra::shared($className, true);
	}

	/**
	 * wypisanie danych
	 * 
	 * @param string $layout - nazwa formatowania, ktore ma byc uzyte
	 */
	public function write($layout='_default') {
		$args = func_get_args();
		$args[0] = $this->data;
		$classMethod = array($this->tpl, $layout);
		return call_user_func_array($classMethod, $args);
	}

	/**
	 * sformatowanie danych
	 * 
	 * @param string $layout - nazwa formatowania, ktore ma byc uzyte
	 * @return string
	 */
	public function render($layout='_default') {
		ob_start();
		$this->write($layout);
		$out = ob_get_contents();
		ob_clean();
		return $out;
	}
}
