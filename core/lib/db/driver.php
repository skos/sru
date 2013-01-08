<?php
/**
 * sterownik do bazy danych
 */
abstract class UFlib_Db_Driver {

	/**
	 * polaczenie do bazy
	 */
	protected $connection;

	/**
	 * adres
	 */
	protected $host;
	
	/**
	 * nazwa uzytkownika
	 */
	protected $user;
	
	/**
	 * haso
	 */
	protected $pass;
	
	/**
	 * nazwa bazy
	 */
	protected $base;

	/**
	 * kodowanie
	 */
	protected $encoding;

	/**
	 * porownywanie stringow
	 */
	protected $collation;
	
	/**
	 * czy polaczenie ma byc permanentne
	 */
	protected $pconnect;

	/**
	 * czy automatycznie nawiazywac polaczenie
	 */
	protected $autoconnect;

	/**
	 * rzutuje dane z bazy na php-owe typy
	 */
	protected $normalizer;

	/**
	 * prefix nazw tabel
	 */
	protected $prefix = '';

	public function __construct($params) {
		/*
		if (is_null($params)) {
			return;
		}
		*/
		$this->normalizer =& $this->chooseNormalizer();
		$this->autoconnect = false;
		if (isset($params['host'])      && is_string($params['host']) &&
		    isset($params['user'])      && is_string($params['host']) &&
			isset($params['pass'])      && is_string($params['host']) &&
			isset($params['base'])      && is_string($params['host']) &&
			isset($params['encoding'])  && is_string($params['encoding']) &&
			isset($params['collation']) && is_string($params['collation']) &&
			isset($params['pconnect'])  && is_bool($params['pconnect'])) {
			$this->host      = $params['host'];
			$this->user      = $params['user'];
			$this->pass      = $params['pass'];
			$this->base      = $params['base'];
			$this->encoding  = $params['encoding'];
			$this->collation = $params['collation'];
			$this->pconnect  = $params['pconnect'];
			
			if (isset($params['autoconnect']) && (true === $params['autoconnect'])) {
				$this->autoconnect = true;
			} else {
				$this->connect($this->host, $this->user, $this->pass, $this->base, $this->encoding, $this->collation, $this->pconnect);
			}
			if (isset($params['prefix']) && is_string($params['prefix'])) {
				$this->prefix = $params['prefix'];
			}
		} else {
			throw new UFex_Db_BadConfig('Bad DB config');
		}
	}

	protected function &chooseNormalizer() {
		return UFra::shared('UFlib_DaoNormalizer');
	}

	/**
	 * nawiazanie polaczenia z baza
	 * 
	 * @param string $host - adres
	 * @param string $user - uzytkownik
	 * @param string $pass - haslo
	 * @param string $base - nazwa bazy
	 * @param string $encoding - kodowanie bazy
	 * @param string $collation - sposob porownywania stringow
	 * @param bool $pconnect - czy permanentne polaczenie?
	 */
	abstract public function connect($host, $user, $pass, $base, $encoding, $collation, $pconnect = false);

	/**
	 * rozlaczenie z baaza
	 */
	abstract public function disconnect();

	/**
	 * zmiana uzywanej bazy
	 * 
	 * @param string $base - nazwa bazy
	 * @param string $encoding - kodowanie bazy
	 * @param string $collation - sposob porownywania stringow
	 */
	abstract public function database($base, $encoding, $collation);

	/**
	 * wykonuje select
	 * 
	 * @param UFlib_Db_Query $query - dane zapytania
	 */
	abstract public function select(UFlib_Db_Query $query);

	/**
	 * wykonuje insert
	 * 
	 * @param UFlib_Db_Query $query - dane zapytania
	 */
	abstract public function insert(UFlib_Db_Query $query);

	/**
	 * wykonuje update
	 * 
	 * @param UFlib_Db_Query $query - dane zapytania
	 */
	abstract public function update(UFlib_Db_Query $query);

	/**
	 * wykonuje delete 
	 * 
	 * @param UFlib_Db_Query $query - dane zapytania
	 */
	abstract public function delete(UFlib_Db_Query $query);

	/**
	 * escape'uje niebezpieczne znaki w parametrach zapytania
	 * 
	 * @param string $val - tresc parametru
	 * @return string - zabezpieczony parametr zapytania
	 */
	abstract public function escape($val);

	/**
	 * automatycznie nawiazuje polaczenie, jezeli go nie ma
	 *
	 * @throws UFex_Db_NotConnected - autoconnect wylaczony i nie ma polaczenia
	 */
	protected function autoconnect() {
		if (is_null($this->connection)) {
			if (true === $this->autoconnect) {
				$this->connect($this->host, $this->user, $this->pass, $this->base, $this->encoding, $this->collation, $this->pconnect);
			} else {
				throw new UFex_Db_NotConnected('Not connected (no autoconnection)', 0, E_ERROR);
			}
		}
	}
}
