<?php
/**
 * klasa dostepu do danych
 */
abstract class UFdaoBase
extends UFlib_ClassWithService {

	/**
	 * klasa walidujaca dane
	 */
	protected $validator = null;

	/**
	 * prefix nazwy klasy mapowania
	 */
	protected $mappingPrefix;

	/**
	 * obiekt rzutujacy dane na prawidlowe typy
	 */
	protected $normalizer;
	
	/**
	 * cache danych z dao
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

	/**
	 * cache na uzyte mappingi
	 */
	protected $mappings = array();

	public function __construct(&$srv=null) {
		parent::__construct($srv);
		$this->db = $this->chooseDb();
		$this->validator = $this->chooseValidator();
		$this->mappingPrefix = $this->chooseMappingPrefix();
		$this->normalizer = $this->chooseNormalizer();
		$this->cache = $this->chooseCache();
		$this->cacheTtl = $this->chooseCacheTtl();
		$this->cachePrefix = $this->chooseCachePrefix();
	}

	/**
	 * wybor obiektu bazy danych obslugujacej to dao
	 * 
	 * @access protected
	 * @return void
	 */
	protected function &chooseDb() {
		return $this->_srv->get('db');
	}

	/**
	 * wybiera normalizator danych
	 * 
	 * @return UF*
	 */
	protected function &chooseNormalizer() {
		return UFra::shared('UFlib_DaoNormalizer');
	}

	/**
	 * wybiera walidator danych
	 * 
	 * @return UF*
	 */
	protected function &chooseValidator() {
		return UFra::shared('UFlib_DaoValidator');
	}

	/**
	 * wybiera przedrostek nazwy obiektu mapujacego
	 * 
	 * @return string
	 */
	protected function chooseMappingPrefix() {
		$class = get_class($this);
		$class = explode('_', $class, 2);
		return $class[1];
	}

	/**
	 * wybiera typ cache'u
	 * 
	 * @return UFlib_Cache*
	 */
	protected function &chooseCache() {
		return UFra::shared('UFlib_Cache'.$this->_srv->get('conf')->cacheDao);
	}

	/**
	 * wybiera czas zycia danych w cache'i
	 * 
	 * @return int
	 */
	protected function chooseCacheTtl() {
		return 10;
	}

	/**
	 * prefix cache'ow
	 * @return string
	 */
	protected function chooseCachePrefix() {
		return get_class($this);
	}

	/**
	 * pobiera mapowanie
	 *
	 * wywolania sa cache'owane
	 * 
	 * @param string $mapping - nazwa mapowania do pobrania
	 * @return Map - mapowanie
	 */
	protected function &mapping($mapping) {
		if (isset($this->mappings[$mapping])) {
			$map =& $this->mappings[$mapping];
		} else {
			$map = UFra::shared('UFmap_'.$this->mappingPrefix.'_'.ucfirst($mapping));
			$this->mappings[$mapping] =& $map;
		}
		return $map;
	}

	/**
	 * sprawdza poprawnosc danej
	 * 
	 * @param string $var - zmienna
	 * @param mixed $val - wartosc
	 * @param mixed $change - zmiana wartosci?
	 * @return bool - czy proces walidacji doszedl do konca
	 */
	public function validate($var, &$val, $change) {
		$mapping = $this->mapping($change?'set':'add');
		$type = $mapping->columnType($var);
		if (call_user_func(array($this->validator, 'type'), $type, $val)) {
			$valids = $mapping->valids();
			if (array_key_exists($var, $valids)) {
				foreach ($valids[$var] as $type=>$params) {
					if (!call_user_func(array($this->validator, $type), $val, $params)) {
						throw UFra::factory('UFex_Dao_DataNotValid', 'Data "'.$var.'" is not valid', 0, E_WARNING, array($var => $type));
					}
				}
			}
		} else {
			throw UFra::factory('UFex_Dao_DataNotValid', 'Data type is not valid', 0, E_WARNING, array($var => $type));
		}
		return true;
	}

	/**
	 * rzutuje na odpowiedni typ
	 * 
	 * @param string $var - nazwa danej
	 * @param mixed $val - wartosc
	 * @param mixed $change - zmiana wartosci?
	 * @return mixed - zrzutowana dana
	 */
	public function normalize($var, &$val, $change) {
		$mapping = $this->mapping($change?'set':'add');
		$type = $mapping->columnType($var);
		return $this->normalizer->fill2dao($val, $type);
	}

	/**
	 * wylicza offset
	 * 
	 * @param int $page - ktora strona wynikow
	 * @param int $perPage - ile wynikow na stronie
	 * @return int - offset
	 */
	protected function findOffset($page, $perPage) {
		return $perPage * ($page-1);
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
