<?
/**
 * alias komputera
 */
class UFmap_SruAdmin_ComputerAlias_Get
extends UFmap {
	protected $columns = array(
		'id'		=> 'a.id',
		'computerId'	=> 'a.computer_id',
		'host'		=> 'a.host',
		'isCname'	=> 'a.is_cname',
		'ip'		=> 'c.ipv4',
		'parent'	=> 'c.host',
		'parentComment'	=> 'c.comment',
		'parentBanned'	=> 'c.banned',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'computerId'	=> self::INT,
		'host'		=> self::TEXT,
		'isCname'	=> self::BOOL,
		'ip'		=> self::TEXT,
		'parent'	=> self::TEXT,
		'parentComment'	=> self::TEXT,
		'parentBanned'	=> self::BOOL,
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
