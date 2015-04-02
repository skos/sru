<?
/**
 * wyciagniecie pojedynczego pokoju
 */
class UFmap_Sru_Location_Get
extends UFmap {

	protected $columns = array(
		'id'             => 'id',
		'alias'          => 'alias',
		'dormitoryId'    => 'dormitory_id',
		'usersCount'     => 'users_count',
		'usersMax'	 => 'users_max',
		'computersCount' => 'computers_count',
		'typeId'	 => 'type_id',
		'comment'        => 'comment',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'alias'          => self::TEXT,
		'dormitoryId'    => self::INT,
		'usersCount'     => self::INT,
		'usersMax'	 => self::INT,
		'computersCount' => self::INT,
		'typeId'	 => self::INT,
		'comment'        => self::TEXT,
	);
	protected $tables = array(
		'' => 'locations',
	);
	protected $pk = 'id';
}
