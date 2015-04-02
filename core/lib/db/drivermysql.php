<?php
/**
 * sterownik do bazy mysql
 */
class UFlib_Db_DriverMysql
extends UFlib_Db_Driver {
	
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
					$column = 'UNIX_TIMESTAMP('.$column.')';
					break;
			}
			$columns[] = $column.' AS `'.$alias.'`';
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
	 * @param string $queryWhere - warunki
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
				$value = ($value?'TRUE':'FALSE');
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
				$value = 'FROM_UNIXTIME('.(int)$value.')';
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
		$cols = array($query->pkName => $query->pk) + $query->columns;
		$types = array($query->pkName => $query->pkType) + $query->columnTypes;
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
			} catch (UFex_Db_NotConnected $e) {
				throw new UFex_Db_NotEscaped('Not escaped: not connected to the database', 0, E_WARNING);
			}
		}
		$result = mysql_real_escape_string($val, $this->connection);
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
		if (!mysql_close($this->connection)) {
			throw new UFex_Db_NotDisconnected('Could not disconnect: '.mysql_error($this->connection), mysql_errno($this->connection), E_WARNING);
		}
		$this->connection = null;
	}
	
	/**
	 * @see UFlib_Db_Driver::connect()
	 */
	public function connect($host, $user, $pass, $base, $encoding, $collation, $pconnect = false, $port=3306) {
		if (!is_null($this->connection)) {
			$this->disconnect();
		}
		if (is_int($port)) {
			$host .= ':'.$port;
		}
		UFra::debug('DB connect...');
		if ($pconnect) {
			$this->connection = @mysql_pconnect($host, $user, $pass);
		} else {
			$this->connection = @mysql_connect($host, $user, $pass);
		}
		if (false === $this->connection) {
			$this->connection = null;
			throw new UFex_Db_NotConnected('Could not connect: '.mysql_error(), 0, E_ERROR);
		}
		$this->database($base, $encoding, $collation);
		UFra::debug('...DB connect');
	}

	/**
	 * @see UFlib_Db_Driver::database()
	 * @throws UFex_Db_BaseNotChanged - nie udalo sie zmienic bazy
	 */
	public function database($base, $encoding, $collation) {
		$this->autoconnect();
		if (!@mysql_select_db($base, $this->connection)) {
			throw new UFex_Db_BaseNotChanged('Could not change database: '.mysql_error($this->connection), mysql_errno($this->connection), E_ERROR);
		}

		// ustawienie opcji dotyczacych kodowania
		$this->query('SET NAMES "'.$encoding.'" COLLATE "'.$collation.'"');
		// to chyba nie jest potrzebne, bo psuje polskie literki
		//$this->query('SET CHARACTER SET "'.$encoding.'"');
	}
	
	/**
	 * zamienia mysql-we kody bledu na stale zdefiniowane w wyjatku
	 * 
	 * @param string $code - kod bledu
	 * @return int
	 */
	protected function error($code) {
		return $code;
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
		$result = @mysql_query($sql);
		UFra::debug($sql);
		if (false === $result) {
			$errorMsg = mysql_error($this->connection);
			$errorNo = $this->error(mysql_errno($this->connection));
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
	 * @throws UFex_Db_QueryFailed - zapytanie nie powiodlo sie
	 */
	public function select(UFlib_Db_Query $query) {
		$sql = $this->prepareSelect($query);

		$result = $this->query($sql);
		
		$return = array();
		$types = $query->columnTypes;
		while ($row = mysql_fetch_assoc($result)) {
			$return[] = $this->normalizer->db2daoRow($row, $types);
		}
		return $return;
	}

	/**
	 * wykonanie zapytania typu insert
	 * 
	 * @param UFlib_Db_Query $query - dane zapytania
	 * @return int/null - id (z pola typu autoincrement) wstawionego wiersza
	 * @throws UFex_Db_QueryFailed - zapytanie nie powiodlo sie
	 */
	public function insert(UFlib_Db_Query $query, $lastVal = true) {
		$sql = $this->prepareInsert($query);

		$result = $this->query($sql);

		if ($lastVal) {
			return (int)mysql_insert_id($this->connection);
		} else {
			return null;
		}
	}

	/**
	 * wykonanie zapytania typu update
	 * 
	 * @param UFlib_Db_Query $query - dane zapytania
	 * @return int - ilosc zmodyfikowanych wierszy
	 * @throws UFex_Db_QueryFailed - zapytanie nie powiodlo sie
	 */
	public function update(UFlib_Db_Query $query) {
		$sql = $this->prepareUpdate($query);

		$result = $this->query($sql);

		return (int)@mysql_affected_rows($this->connection);
	}

	/**
	 * wykonanie zapytania typu delete
	 * 
	 * @param UFlib_Db_Query $query - dane zapytania
	 * @return int - ilosc zmodyfikowanych wierszy
	 * @throws UFex_Db_QueryFailed - zapytanie nie powiodlo sie
	 */
	public function delete(UFlib_Db_Query $query) {
		$sql = $this->prepareDelete($query);

		$result = $this->query($sql);

		return (int)@mysql_affected_rows($this->connection);
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
