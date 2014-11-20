<?
/**
 * wyciagniecie pojedynczego akademika
 */
class UFmap_Sru_Dormitory_Get
extends UFmap {

	protected $columns = array(
		'id'           => 'id',
		'name'         => 'name',
		'alias'        => 'alias',
		'userCount'    => 'users_count',
		'computerCount' => 'computers_count',
		'usersMax'     => 'users_max',
		'computersMax' => 'computers_max',
		'displayOrder' => 'display_order',
	);
	protected $columnTypes = array(
		'id'           => self::INT,
		'name'         => self::TEXT,
		'alias'        => self::TEXT,
		'userCount'    => self::INT,
		'computerCount' => self::INT,
		'usersMax'     => self::INT,
		'computersMax' => self::INT,
		'displayOrder' => self::INT,
	);
	protected $tables = array(
		'' => 'dormitories',
	);
	protected $pk = 'id';
}
