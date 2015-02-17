<?
/**
 * wyciagniecie pojedynczego akademika
 */
class UFmap_Sru_Dormitory_Get
extends UFmap {

	protected $columns = array(
		'id'           => 'd.id',
		'name'         => 'd.name',
		'alias'        => 'd.alias',
		'userCount'    => 'd.users_count',
		'computerCount' => 'd.computers_count',
		'usersMax'     => 'd.users_max',
		'computersMax' => 'd.computers_max',
		'displayOrder' => 'd.display_order',
		'campusId'	=> 'd.campus',
		'campusName'	=> 'c.name',
		'active'	=> 'd.active',
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
		'displayOrder' => self::INT,
		'campusId'	=> self::INT,
		'campusName'	=> self::TEXT,
		'active'	=> self::BOOL,
	);
	protected $tables = array(
		'd' => 'dormitories',
	);
	protected $joins = array(
		'c' => 'campuses',
	);
	protected $joinOns = array(
		'c' => 'd.campus=c.id',
	);
	protected $pk = 'id';
}
