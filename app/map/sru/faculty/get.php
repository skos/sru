<?
/**
 * wyciagniecie pojedynczego wydzialu
 */
class UFmap_Sru_Faculty_Get
extends UFmap {

	protected $columns = array(
		'id'             => 'id',
		'name'           => 'name',
		'alias'          => 'alias',
		'usersCount'     => 'users_count',
		'computersCount' => 'computers_count',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'name'           => self::TEXT,
		'alias'          => self::TEXT,
		'usersCount'     => self::INT,
		'computersCount' => self::INT,
	);
	protected $tables = array(
		'' => 'faculties',
	);
	protected $pk = 'id';
}
