<?php

/**
 * zapytanie do bazy
 */
class UFlib_Db_Query {
	
	/**
	 *  porownania
	 */
	const SQL = 0;	// "goly" sql
	const EQ = 1;	// rowne
	const IN = 2;	// zawiera sie w liscie
	const LT = 3;	// mniejsze
	const NOT_GTE = 3;
	const GT = 4;	// wieksze
	const NOT_LTE = 4;
	const LTE = 5;	// mniejsze lub rowne
	const NOT_GT= 5;
	const GTE = 6;	// wieksze lub rowne
	const NOT_LT = 6;
	const NOT_EQ = 7;	// nie rowne
	const NOT_IN = 8;	// nie zawiera sie w liscie
	const LIKE = 9;	// podobne

	/**
	 * kolejnosc sortowania
	 */
	const ASC = true;
	const DESC = false;
	
	/**
	 * dane
	 */
	protected $data = array(
		'tables'      => array(),
		'joins'       => array(),
		'joinOns'     => array(),
		'wheres'      => array(),
		'comments'    => array(),
		'distinct'    => false,
		'groupBys'    => array(),
		'columns'     => array(),
		'values'      => array(),
		'columnBinds' => array(),
		'columnTypes' => array(),
		'orders'      => array(),
		'limit'       => false,
		'offset'      => 0,
		'raw'         => null,
		'pk'          => '',
		'pkName'      => 'id',
		'pkType'      => UFmap::INT,

		'DESC'        => self::DESC,
		'ASC'         => self::ASC,

		'SQL'         => self::SQL,
		'EQ'          => self::EQ,
		'IN'          => self::IN,
		'LT'          => self::LT,
		'NOT_GTE'     => self::NOT_GTE,
		'GT'          => self::GT,
		'NOT_LTE'     => self::NOT_LTE,
		'LTE'         => self::LTE,
		'NOT_GT'      => self::NOT_GT,
		'GTE'         => self::GTE,
		'NOT_LT'      => self::NOT_LT,
		'NOT_EQ'      => self::NOT_EQ,
		'NOT_IN'      => self::NOT_IN,
		'LIKE'        => self::LIKE,
	);

	public function __get($var) {
		return $this->data[$var];
	}

	/**
	 * zapytanie w czystym sql-u
	 * 
	 * @param string $query - zapytanie
	 */
	public function raw($query) {
		$this->raw = $query;
	}

	/**
	 * dodaje jedna kolumne grupowania
	 * 
	 * @param string $col - nazwa kolumny
	 */
	public function groupBy($col) {
		$this->data['groupBys'][] = $col;
	}

	/**
	 * dodaje warunkek
	 * 
	 * @param string $col - kolumna
	 * @param mixed/null $val - wartosc. null - $col uznawane jest za fragment
	 *                          sql-a, ktory nie bedzie potem przetwarzany
	 * @param int $operand - typ porownania
	 */
	public function where($col, $val=null, $operand=self::EQ) {
		$this->data['wheres'][] = array($col, $val, $operand);
	}

	/**
	 * dodaje komentarz
	 * 
	 * @param string $text - tekst komentarza
	 */
	public function comment($text) {
		$this->data['comments'][] = $text;
	}

	/**
	 * definiuje unikalnosc wynikow
	 * 
	 * @param bool $distinct - czy maja byc unikalne?
	 * @throws UFex_Db_BadQueryParam - brak wymaganego parametru
	 */
	public function distinct($distinct = true) {
		$this->data['distinct'] = (bool)$distinct;
	}
	
	/**
	 * dodaje kolejnosc
	 * 
	 * @param string $column - kolumna
	 * @param int $order - kolejnosc
	 * @throws UFex_Db_BadQueryParam - brak wymaganego parametru
	 */
	public function order($column, $order = self::ASC) {
		$this->data['orders'][$column] = $order;
	}

	/**
	 * definiuje ilosc zwroconych wynikow
	 * 
	 * @param int $count 
	 * @throws UFex_Db_BadQueryParam - brak wymaganego parametru
	 */
	public function limit($count) {
		$this->data['limit'] = (int)$count;
	}

	/**
	 * definiuje offset wynikow
	 * 
	 * @param int $count - wielkosc offsetu
	 * @throws UFex_Db_BadQueryParam - brak wymaganego parametru
	 */
	public function offset($count) {
		$this->data['offset'] = (int)$count;
	}

	/**
	 * definiuje kolumny
	 * 
	 * @param array $columns - lista kolumn w formacie alias=>nazwa
	 * @param array $types - typy
	 * @param array $binds - bindingi
	 * @throws UFex_Db_BadQueryParam - brak wymaganego parametru
	 */
	public function columns(array $columns, array $types, array $binds=array()) {
		foreach ($columns as $alias=>$col) {
			$this->data['columns'][$alias]     = $col;
			$this->data['columnTypes'][$alias] = $types[$alias];
			if (isset($binds[$alias])) {
				$this->data['columnBinds'][$alias] = $binds[$alias];
			}
		}
	}

	/**
	 * definiuje tabele
	 * 
	 * @param array $tables - lista tabel w formacie alias=>nazwa
	 */
	public function tables(array $tables) {
		$this->data['tables'] = $tables;
	}

	/**
	 * definiuje wartosci
	 * 
	 * @param array $columns - nazwy kolumn
	 * @param array $values - wartosci w formacie kolumna=>wartosc
	 * @param array $types - typy
	 */
	public function values(array $columns, array $values, array $types) {
		foreach ($columns as $alias=>$col) {
			$this->data['columns'][$alias]     = $col;
			$this->data['columnTypes'][$alias] = $types[$alias];
			if (!array_key_exists($alias, $values)) {
				continue;
			}
			$this->data['values'][$alias]      = $values[$alias];
		}
	}

	/**
	 * definiuje parametry zlaczen
	 * 
	 * @param array $tables - tabele
	 * @param array $aliases - aliasy
	 * @param array $ons - warunki
	 * @throws UFex_Db_BadQueryParam - brak wymaganego parametru
	 */
	public function joins(array $tables, array $ons) {
		$this->data['joins']   = $tables;
		$this->data['joinOns'] = $ons;
	}

	/**
	 * definiuje klucz glowny
	 * 
	 * @param string $column - nazwa kolumny w bazie
	 * @param int $type - typ zmiennej
	 */
	public function pk($column, $type) {
		$this->data['pk'] = $column;
		$this->data['pkType'] = $type;
	}
}
