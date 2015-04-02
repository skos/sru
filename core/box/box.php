<?php
abstract class UFbox
extends UFlib_ClassWithService {
	
	/**
	 * template
	 */
	protected $tpl;
	
	/**
	 * cache wygenerowanych html-i
	 */
	protected $cache;

	/**
	 * czas zycia danych w cache'u
	 */
	protected $cacheTtl;

	/**
	 * prefix danych w cache'u
	 */
	protected $cachePrefix;


	public function __construct(&$srv = null) {
		parent::__construct($srv);
		$this->tpl = $this->chooseTemplate();
		$this->cache = $this->chooseCache();
		$this->cacheTtl = $this->chooseCacheTtl();
		$this->cachePrefix = $this->chooseCachePrefix();
		$this->req = $this->_srv->get('req');
	}
	
	public function __call($func, $params) {
		return $this->render($func);
	}

	
	/**
	 * sprawdza czy jest nastepna i poprzednia strona wynikow
	 * 
	 * @param UFbeanList $bean - dane
	 * @param int $page - strona
	 * @param mixed $perPage - danych na stronie
	 * @return array - dane do nawigacji
	 */
	protected function _checkNavigation(UFbeanList &$bean, $page, $perPage) {
		$d = array();
		if (isset($bean[$perPage])) {
			unset($bean[$perPage]);
			$d['pageNext'] = true;
		} else {
			$d['pageNext'] = false;
		}
		if ($page > 1) {
			$d['pagePrev'] = true;
		} else {
			$d['pagePrev'] = false;
		}
		$d['page'] = (int)$page;
		return $d;
	}

	/**
	 * wybor template'a
	 * 
	 * @return UFtpl_*
	 */
	protected function chooseTemplate() {
		$className = get_class($this);
		$className = 'UFtpl'.stristr($className, '_');
		return UFra::shared($className, true);
	}

	/**
	 * wybiera typ cache'u
	 * 
	 * @return UFlib_Cache*
	 */
	protected function chooseCache() {
		return UFra::shared('UFlib_Cache'.$this->_srv->get('conf')->cacheBox);
	}

	/**
	 * wybiera czas zycia danych w cache'i
	 * 
	 * @return int
	 */
	protected function chooseCacheTtl() {
		return 60;
	}

	/**
	 * prefix cache'ow
	 * @return string
	 */
	protected function chooseCachePrefix() {
		return get_class($this);
	}

	/**
	 * generuje tresc przesylana do klienta
	 *
	 * @param string $layout - nazwa 
	 * @param array $data - dane do wypisania
	 * @param array $other - dodatkowe zmienne do ustawienia
	 */
	protected function render($layout, array $data=array()) {
		try {
			ob_start();
			$this->tpl->{$layout}($data);
			$out = ob_get_contents();
			ob_clean();
			return $out;
		} catch (Ex $e) {
			return '/BOX FAILED/';
			UFra::warning('Box failed: '.get_class($this->tpl).'/'.$layout.' '.print_r($data, true));
		}
	}

	/**
	 * pobiera dana z z cache'u
	 * 
	 * @param string $var - klucz
	 * @return mixed - dana
	 */
	protected function cacheGet($var) {
		return $this->cache->get($var);
	}

	/**
	 * dodaje dana do cache'u
	 * 
	 * @param string $var - klucz
	 * @param mixed $val - dana
	 * @param int/null $ttl - czas zycia tej danej
	 */
	protected function cacheSet($var, $val, $ttl=null) {
		if (!is_int($ttl)) {
			$ttl = $this->cacheTtl;
		}
		$this->cache->set($var, $val, $ttl);
	}

	/**
	 * sprawdza, czy w cache'u jest dana
	 * 
	 * @param string $var - klucz
	 * @return bool
	 */
	protected function cacheIs($var) {
		return $this->cache->is($var);
	}

	/**
	 * usuwa dana z cache'u
	 * 
	 * @param string $var - klucz
	 */
	protected function cacheDel($var) {
		$this->cache->del($var);
	}
}
