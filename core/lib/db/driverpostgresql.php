<?php
/**
 * sterownik do bazy postgresql
 */
class UFlib_Db_DriverPostgresql
extends UFlib_Db_Driver {

	/**
	 * odstep pomiedzy sprawdzeniami zajetosci bazy [us]
	 */
	const BUSY_CHECK_INTERVAL = 2000;
	
	/**
	 * punkty kontrolne transakcji
	 *
	 * uzywane do zagniezdzania transakcji
	 */
	protected $savepoints = array();

	/**
	 * parsuje dane dotyczace kolumn
	 * 
	 * @param array $queryColumn - lista kolumn
	 * @param array $queryColumnTypes - typy
	 * @param array $queryColumnBinds - podstawienia do zmiennych
	 * @return string - fragment zapytania sql
	 */
	protected function parseColumns(array $queryColumn, array $queryColumnTypes, array $queryColumnBinds) {
		$columns = array();
		foreach ($queryColumn as $alias=>$column) {
			if (isset($queryColumnTypes[$alias])) {
				$type = $queryColumnTypes[$alias];
			} else {
				$type = '*';
			}
			switch ($type) {
				case UFmap::NULL_DATE:
				case UFmap::DATE:
				case UFmap::NULL_TS:
				case UFmap::TS:
					$column = 'EXTRACT(EPOCH FROM '.$column.')';
					break;
			}
			$columns[] = $column.' AS "'.$alias.'"';
		}
		$columns = implode(', ', $columns);
		return $columns;
	}

	/**
	 * parsuje dane dotyczace tabel
	 * 
	 * @param array $queryTables - tabele
	 * @return string - framgent zapytania sql
	 * @throws UFex_Db_NoQueryTables - nie podano zadnej tabeli
	 */
	protected function parseTables($queryTables) {
		if (empty($queryTables)) {
			throw new UFex_Db_NoQueryTables('No tables has beed specified in query', 0, E_WARNING);
		}
		$tables = array();
		foreach ($queryTables as $alias=>$table) {
			if ('' === $alias) {
				$tables[] = $this->prefix.$table;
			} else {
				$tables[] = $this->prefix.$table.' AS '.$alias;
			}
		}
		$tables = implode(', ', $tables);
		return $tables;
	}

	/**
	 * parsuje dane dotyczace zlaczenia tabel
	 * 
	 * @param array $queryJoin - zlaczane tabele
	 * @param array $queryOn - warunki zlaczen
	 * @return string - framgent zapytania sql
	 */
	protected function parseJoins(array $queryJoin, array $queryOn) {
		if (empty($queryJoin)) {
			return '';
		}
		$joins = '';
		foreach ($queryJoin as $joinAlias=>$join) {
			if (isset($queryOn[$joinAlias])) {
				$joinOn = ' ON '.$queryOn[$joinAlias];
			}
			$joins .= "\n".'LEFT JOIN '.$this->prefix.$join.' AS '.$joinAlias.$joinOn;
		}
		return $joins;
	}

	/**
	 * parsuje dane dotyczace grupowania
	 * 
	 * @param array $groupBy - aliasy
	 * @param array $columns - lista kolumn
	 * @return string - fragment zapytania sql
	 */
	protected function parseGroupBys(array $groupBy=array(), array $columns) {
		if (empty($groupBy)) {
			return '';
		}
		$tmp = array();
		foreach ($groupBy as $col) {
			$tmp[] = $columns[$col];
		}
		return "\n".'GROUP BY '.implode(', ', $tmp);
	}

	/**
	 * parsuje dane dotyczace kolejnosci
	 * 
	 * @param array $queryOrder - sortowania
	 * @return string - fragment zapytania sql
	 */
	protected function parseOrder(array $queryOrder, array $columns) {
		if (empty($queryOrder)) {
			return '';
		}
		$orders = array();
		foreach ($queryOrder as $alias=>$asc) {
			$orders[] = $columns[$alias].' '.(UFlib_Db_Query::ASC===$asc?'ASC':'DESC');
		}
		return $orders = "\n".'ORDER BY '.implode(', ', $orders);
	}

	/**
	 * parsuje dane dotyczace limitowania ilosci wynikow
	 * 
	 * @param int $queryLimit - ilosc wynikow do zwrocenia
	 * @return string - fragment zapytania sql
	 */
	protected function parseLimit($queryLimit) {
		if (!is_int($queryLimit)) {
			return '';
		}
		return $limit = "\n".'LIMIT '.$queryLimit;
	}

	/**
	 * parsuje dane dotyczace limitowania ilosci wynikow (offset)
	 * 
	 * @param int $queryOffset - przesuniecie
	 * @return string - fragment zapytania sql
	 */
	protected function parseOffset($queryOffset) {
		if (!is_int($queryOffset) || 0 >= $queryOffset) {
			return '';
		}
		$offset = "\n".'OFFSET '.$queryOffset;
		return $offset;
	}
	
	/**
	 * parsuje dane dotyczace unikalnosci wynikow
	 * 
	 * @param bool $queryDistinct - czy wyniki maja byc unikalne?
	 * @return string - fragment zapytania sql
	 */
	protected function parseDistinct($queryDistinct) {
		if (!is_null($queryDistinct) && ($queryDistinct === true)) {
			return ' DISTINCT';
		} else {
			return '';
		}
	}

	/**
	 * parsuje dane dotyczace warunkow zapytania
	 * 
	 * @param array $queryWhere - warunki
	 * @param array $columns - kolumny
	 * @param array $columnTypes - typy kolumn
	 * @return string - fragment zapytania sql
	 */
	protected function parseWheres(array $queryWhere, array $columns, array $columnTypes) {
		if (!is_array($queryWhere) || empty($queryWhere)) {
			return '';
		}
		$wheres = array();
		foreach ($queryWhere as $where) {
			$column = $where[0];
			$value  = $where[1];
			$operand = $where[2];
			if (UFlib_Db_Query::SQL == $operand) {
				$wheres[] = $column;
				continue;
			}
			$type = $columnTypes[$column];
			$columnDb = $columns[$column];
			if (UFlib_Db_Query::IN === $operand || UFlib_Db_Query::NOT_IN === $operand) {
				$valueDb = array();
				if (is_array($value)) {
					foreach ($value as $v) {
						$valueDb[] = $this->normalizeValue($v, $type);
					}
				} else {
					$valueDb[] = $this->normalizeValue($v, $type);
				}
			} else {
				$valueDb = $this->normalizeValue($value, $type);
			}
			switch ($operand) {
				case UFlib_Db_Query::LIKE:
					if (UFmap::TEXT == $type) {
							$wheres[] = $columnDb.' LIKE '.$valueDb;
						break;
					}
				case UFlib_Db_Query::EQ:
					if ('NULL' === $valueDb) {
						$wheres[] = $columnDb.' IS '.$valueDb;
					} else {
						$wheres[] = $columnDb.'='.$valueDb;
					}
					break;
				case UFlib_Db_Query::NOT_EQ:
					if ('NULL' === $valueDb) {
						$wheres[] = $columnDb.' IS NOT '.$valueDb;
					} else {
						$wheres[] = $columnDb.'!='.$valueDb;
					}
					break;
				case UFlib_Db_Query::IN:
					$wheres[] = $columnDb.' IN ('.implode(',', $valueDb).')';
					break;
				case UFlib_Db_Query::NOT_IN:
					$wheres[] = $columnDb.' NOT IN ('.implode(',', $valueDb).')';
					break;
				case UFlib_Db_Query::LT:
					$wheres[] = $columnDb.'<'.$valueDb;
					break;
				case UFlib_Db_Query::GT:
					$wheres[] = $columnDb.'>'.$valueDb;
					break;
				case UFlib_Db_Query::LTE:
					$wheres[] = $columnDb.'<='.$valueDb;
					break;
				case UFlib_Db_Query::GTE:
					$wheres[] = $columnDb.'>='.$valueDb;
					break;
				default:
					throw UFra::factory('UFex_Db_BadQueryParam', 'Unknown operand');
					break;
			}
		}
		return "\n".'WHERE '.implode(' AND ', $wheres);
	}

	/**
	 * parsuje dane dotyczace wartosci dla zapytania typu "update"
	 * 
	 * jezeli zachodzi taka koniecznosc, to dane sa rzutowane, escape'owane itp.
	 *
	 * @param array $queryColumns - lista kolumn
	 * @param array $queryValues - wartosci
	 * @param array $queryTypes - typy
	 * @return string - fragmemt zapytania sql
	 * @throws UFex_Db_NoQueryValues - nie podano wartosci
	 */
	protected function parseValues(array $queryColumns, array $queryValues, array $queryTypes) {
		if (empty($queryColumns)) {
			throw UFra::factory('UFex_Db_NoQueryValues', 'No values for insert');
		}
		$sets = array();
		$values = $this->normalizeValues($queryColumns, $queryValues, $queryTypes);
		foreach ($values as $column=>$val) {
			$sets[] = $column.' = '.$val;
		}
		return implode(', ', $sets);
	}

	/**
	 * parsuje dane dotyczace wartosci dla zapytania typu "insert"
	 * 
	 * jezeli zachodzi taka koniecznosc, to dane sa rzutowane, escape'owane itp.
	 *
	 * @param array $queryColumns - lista kolumn
	 * @param array $queryValues - wartosci
	 * @param array $queryTypes - typy
	 * @return array(string,string) - fragmenty zapytania sql: pola, wartosci
	 * @throws UFex_Db_NoQueryValues - nie podano wartosci
	 */
	protected function parseValuesSplited(array $queryColumns, array $queryValues, array $queryTypes) {
		if (empty($queryColumns)) {
			throw new UFex_Db_NoQueryValues('No values for insert', 0, E_WARNING);
		}
		$sets = array();
		$values = $this->normalizeValues($queryColumns, $queryValues, $queryTypes);
		return array(implode(',', array_keys($values)), implode(', ', $values));
	}

	/**
	 * rzutuje php-owy typ na bazodanowy
	 * 
	 * @param mixed $value - dana
	 * @param int $type - typ
	 * @return mixed - zrzutowana wartosc
	 */
	protected function normalizeValue($value, $type) {
		switch ($type) {
			case UFmap::NULL_REAL:
				if (is_null($value)) {
					$value = 'NULL';
					break;
				}
			case UFmap::REAL:
				$value = (float)$value;
				break;
			case UFmap::NULL_BOOL:
				if (is_null($value)) {
					$value = 'NULL';
					break;
				}
			case UFmap::BOOL:
				$value = ($value?'\'t\'':'\'f\'');
				break;
			case UFmap::NULL_DATE:
				if (is_null($value)) {
					$value = 'NULL';
					break;
				}
			case UFmap::DATE:
				$value = "'".date('Y-m-d', (int)$value)."'";
				break;
			case UFmap::NULL_TS:
				if (is_null($value)) {
					$value = 'NULL';
					break;
				}
			case UFmap::TS:
				$value = 'TO_TIMESTAMP('.(int)$value.')';
				break;
			case UFmap::RAW:
				// nic nie rob
				break;
			case UFmap::NULL_INT:
				if (is_null($value)) {
					$value = 'NULL';
					break;
				}
			case UFmap::INT:
				$value = (int)$value;
				break;
			case UFmap::NULL_TEXT:
				if (is_null($value)) {
					$value = 'NULL';
					break;
				}
			default:
				$value = '\''.$this->escape($value).'\'';
				break;
		}
		return $value;
	}

	/**
	 * rzutuje typy php-owe na bazodanowe
	 * 
	 * @param array $queryColumns - lista kolumn
	 * @param array $queryValues - wartosci
	 * @param array $queryTypes - typy
	 * @return array - lista wartosc gotowych do wstawienia do zapytania
	 */
	protected function normalizeValues(array $queryColumns, array $queryValues, array $queryTypes) {
		$values = array();
		foreach ($queryValues as $id=>$val) {
			$values[$queryColumns[$id]] = $this->normalizeValue($val, $queryTypes[$id]);
		}
		return $values;
	}

	/**
	 * parsuje dane komentarza
	 * 
	 * @param string $comment - komentarz
	 * @return string - komentarzy sql-owy
	 */
	protected function parseComments($comment) {
		if (!is_string($comment) || '' === $comment) {
			return '';
		}
		return ' /* '.$comment.' */';
	}

	/**
	 * przygowowuje select
	 * 
	 * @param UFlib_Db_Query $query - dane zapytania
	 * @return string - zapytanie sql
	 */
	protected function prepareSelect(UFlib_Db_Query $query) {
		if (is_string($query->raw)) {
			return $query->raw;
		}
		$comment  = $this->parseComments($query->comments);
		$distinct = $this->parseDistinct($query->distinct);
		$columns  = $this->parseColumns($query->columns, $query->columnTypes, $query->columnBinds);
		$tables   = $this->parseTables($query->tables);
		$join     = $this->parseJoins($query->joins, $query->joinOns);
		$where    = $this->parseWheres($query->wheres, $query->columns, $query->columnTypes);
		$order    = $this->parseOrder($query->orders, $query->columns);
		$limit    = $this->parseLimit($query->limit);
		$offset   = $this->parseOffset($query->offset);
		$groupBy  = $this->parseGroupBys($query->groupBys, $query->columns);

		$sql = 'SELECT'.$comment.$distinct.' '.$columns.
			"\nFROM ".$tables.
			$join.
			$where.
			$groupBy.
			$order.
			$limit.
			$offset;
			
		return $sql;
	}

	/**
	 * przygotowuje insert
	 * 
	 * @param UFlib_Db_Query $query - dane zapytania
	 * @return string - zapytanie sql
	 */
	protected function prepareInsert(UFlib_Db_Query $query) {
		if (is_string($query->raw)) {
			return $query->raw;
		}

		$comment = $this->parseComments($query->comments);
		$tables  = $this->parseTables($query->tables);
		list($columns, $values) = $this->parseValuesSplited($query->columns, $query->values, $query->columnTypes);

		$sql = 'INSERT'.$comment.' INTO '.$tables.
			"\n(".$columns.
			")\nVALUES (\n".$values.')';

		return $sql;
	}

	/**
	 * przygotowuje update
	 * 
	 * @param UFlib_Db_Query $query - dane zapytania
	 * @return string - zapytanie sql
	 */
	protected function prepareUpdate(UFlib_Db_Query $query) {
		if (is_string($query->raw)) {
			return $query->raw;
		}

		$comment = $this->parseComments($query->comments);
		$values  = $this->parseValues($query->columns, $query->values, $query->columnTypes);
		$tables  = $this->parseTables($query->tables);
		$cols    = array($query->pkName => $query->pk) + $query->columns;
		$types   = array($query->pkName => $query->pkType) + $query->columnTypes;
		$where   = $this->parseWheres($query->wheres, $cols, $types);

		$sql = 'UPDATE'.$comment.' '.$tables.
			"\nSET ".$values.
			$where;
		
		return $sql;
	}

	/**
	 * przygotowuje delete 
	 * 
	 * @param UFlib_Db_Query $query - dane zapytania
	 * @return string - zapytanie sql
	 */
	protected function prepareDelete(UFlib_Db_Query $query) {
		if (is_string($query->raw)) {
			return $query->raw;
		}

		$comment = $this->parseComments($query->comments);
		$tables  = $this->parseTables($query->tables);
		$cols    = array($query->pkName => $query->pk) + $query->columns;
		$types   = array($query->pkName => $query->pkType) + $query->columnTypes;
		$where   = $this->parseWheres($query->wheres, $cols, $types);

		$sql = 'DELETE'.$comment.' FROM '.$tables.
			$where;
		
		return $sql;
	}

	/**
	 * escape'uje niebezpieczne znaki w parametrach zapytania
	 * 
	 * @param string $val - tresc parametru
	 * @return string - zabezpieczony parametr zapytania
	 * @throws UFex_Db_NotEscaped - nie udalo sie wyescape'owac
	 */
	public function escape($val) {
		if (is_null($this->connection)) {
			try {
				$this->autoconnect();
			} catch (Ex_Db_NotConnected $e) {
				throw new UFex_Db_NotEscaped('Not escaped: not connected to the database', 0, E_WARNING);
			}
		}
		$result = pg_escape_string($this->connection, $val);
		if (false === $result) {
			throw new UFex_Db_NotEscaped('Unknown error', 0, E_WARNING);
		}
		return $result;
	}
	
	/**
	 * rozlaczenie z baaza
	 * 
	 * @throws UFex_Db_NotDisconnected - nie udalo sie rozlaczenie
	 */
	public function disconnect() {
		if (!pg_close($this->connection)) {
			throw new UFex_Db_NotDisconnected('Could not disconnect: '.pg_last_error($this->connection), 0, E_WARNING);
		}
		$this->connection = null;
	}
	
	/**
	 * @see UFlib_Db_Driver::connect()
	 */
	public function connect($host, $user, $pass, $base, $encoding, $collation, $pconnect = false, $port=5432) {
		if (!is_null($this->connection)) {
			$this->disconnect();
		}
		$tmp = 'host='.$host.' user='.$user.' password='.$pass.' dbname='.$base.' port='.$port;
		UFra::debug('DB connect...');
		if ($pconnect) {
			$this->connection = @pg_pconnect($tmp);
		} else {
			$this->connection = @pg_connect($tmp);
		}
		if (false === $this->connection) {
			$this->connection = null;
			throw new UFex_Db_NotConnected('Could not connect to database server: '.pg_last_error(), 0, E_ERROR);
		}
		pg_set_client_encoding($this->connection, $encoding);
		UFra::debug('...DB connect');
	}

	/**
	 * @see UFlib_Db_Driver::database()
	 * @throws UFex_Db_BaseNotChanged - nie udalo sie zmienic bazy
	 */
	public function database($base, $encoding, $collation) {
		throw new Ex_Db_NotSupported('Database change not supported in PostgreSQL. Please reconnect.');
	}
	
	/**
	 * zamienia postgresowe kody bledu na stale zdefiniowane w wyjatku
	 * 
	 * @param string $code - kod bledu
	 * @return int
	 */
	protected function error($code) {
		switch ($code) {
			case '08000':	// CONNECTION EXCEPTION
			case '08003':	// CONNECTION DOES NOT EXIST
			case '08006':	// CONNECTION FAILURE
				return UFex_Db_QueryFailed::ERR_CONNECT;
				break;
			case '42703':	// UNDEFINED COLUMN
			case '42702':	// AMBIGUOUS COLUMN
			case '42P09':	// AMBIGUOUS ALIAS
				return UFex_Db_QueryFailed::ERR_NOCOL;
				break;
			case '42P01':	// UNDEFINED TABLE
				return UFex_Db_QueryFailed::ERR_NOTABLE;
				break;
			case '23505':	// UNIQUE VIOLATION
				return UFex_Db_QueryFailed::ERR_DUPLICATED;
				break;
			case '23502':	// NOT NULL VIOLATION
				return UFex_Db_QueryFailed::ERR_NOFOREIGN;
				break;
			case '23503':	// FOREIGN KEY VIOLATION
				return UFex_Db_QueryFailed::ERR_CONSTRAINT;
				break;
			default:
				return 0;
				break;
		}
	}

	/**
	 * wykonuje zapytanie do bazy
	 * 
	 * @param string $sql - tresc zapytania
	 * @return resource - wynik zapytania
	 * @throws UFex_Db_QueryFailed - zapytanie nie powiodlo sie
	 */
	protected function &query($sql) {
		$this->autoconnect();
		pg_send_query($this->connection, $sql);
		UFra::debug($sql);	// tu i tak musielibysmy poczekac, wiec niech sie cos wykona
		while (pg_connection_busy($this->connection)) {
			usleep(self::BUSY_CHECK_INTERVAL);
		}
		$result = pg_get_result($this->connection);
		$error = pg_result_error_field($result, PGSQL_DIAG_SQLSTATE);
		if (!is_null($error)) {
			$errorMsg = pg_result_error_field($result, PGSQL_DIAG_MESSAGE_PRIMARY);
			$errorNo = $this->error($error);
			throw new UFex_Db_QueryFailed('Query "'.$sql.'" failed: '.$errorMsg, $errorNo, E_ERROR);
		}
		return $result;
	}

	/**
	 * wykonanie zapytania typu select
	 *
	 * dodatkowo metoda dokonuje wrzutowania wynikow na odpowiednie typy
	 * 
	 * @param UFlib_Db_Query $query - dane zapytania
	 * @return array - dane
	 */
	public function select(UFlib_Db_Query $query) {
		$sql = $this->prepareSelect($query);

		$result = $this->query($sql);
		
		$return = array();
		$types = $query->columnTypes;
		while ($row = pg_fetch_assoc($result)) {
			$return[] = $this->normalizer->db2daoRow($row, $types);
		}
		return $return;
	}

	/**
	 * wykonanie zapytania typu insert
	 * 
	 * @param UFlib_Db_Query $query - dane zapytania
	 * @param bool $lastVal - czy pobierac wartosc autoincrement?
	 * @return int/null - id (z pola typu serial) wstawionego wiersza
	 */
	public function insert(UFlib_Db_Query $query, $lastVal = true) {
		$sql = $this->prepareInsert($query);

		$this->query($sql);

		if ($lastVal) {
			$result = $this->query('SELECT lastval()');
			return (int)pg_fetch_result($result, 0, 0);
		} else {
			return null;
		}
	}

	/**
	 * wykonanie zapytania typu update
	 * 
	 * @param UFlib_Db_Query $query - dane zapytania
	 * @return int - ilosc zmodyfikowanych wierszy
	 */
	public function update(UFlib_Db_Query $query) {
		$sql = $this->prepareUpdate($query);
		
		$result = $this->query($sql);

		return (int)pg_affected_rows($result);
	}

	/**
	 * wykonanie zapytania typu delete
	 * 
	 * @param UFlib_Db_Query $query - dane zapytania
	 * @return int - ilosc zmodyfikowanych wierszy
	 */
	public function delete(UFlib_Db_Query $query) {
		$sql = $this->prepareDelete($query);

		$result = $this->query($sql);

		return (int)pg_affected_rows($result);
	}

	/**
	 * rozpoczecie transakcji
	 */
	public function begin() {
		if (count($this->savepoints)) {
			$savepoint = md5(microtime());
			$this->query('SAVEPOINT '.$savepoing);
			array_push($this->savepoints, $savepoint);
		} else {
			$this->query('BEGIN');
		}
	}

	/**
	 * zatwierdzenie transakcji
	 */
	public function commit() {
		if (count($this->savepoints)) {
			$this->query('RELEASE SAVEPOINT '.array_pop($this->savepoints));
		} else {
			$this->query('COMMIT');
		}
	}

	/**
	 * wycofanie transakcji
	 */
	public function rollback() {
		if (count($this->savepoints)) {
			$this->query('ROLLBACK TO SAVEPOINT '.array_pop($this->savepoints));
		} else {
			$this->query('ROLLBACK');
		}
	}
}
