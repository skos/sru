<?
/**
 * wybranie listy aliasów komputera
 */
class UFmap_SruAdmin_ComputerAlias_List
extends UFmap_SruAdmin_ComputerAlias_Get {

	protected $columns = array(
		'id'		=> 'a.id',
		'computerId'	=> 'a.computer_id',
		'host'		=> 'a.host',
		'ip'		=> 'c.ipv4',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'computerId'	=> self::INT,
		'host'		=> self::TEXT,
		'ip'		=> self::TEXT,
	);
	protected $tables = array(
		'a' => 'computers_aliases',
	);
	protected $joins = array(
		'c' => 'computers',
	);
	protected $joinOns = array(
		'c' => 'a.computer_id = c.id',
	);
	protected $pk = 'id';
}
