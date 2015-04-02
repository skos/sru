<?
/**
 * wyciagniecie listy akademików na potrzeby Waleta
 */
class UFmap_Sru_Dormitory_ListWalet
extends UFmap {

	protected $columns = array(
		'dormitoryId'		=> 'id',
		'dormitoryName'		=> 'name',
		'dormitoryAlias'	=> 'alias',
		'displayOrder'		=> 'display_order',
		'active'		=> 'active',
	);
	protected $columnTypes = array(
		'dormitoryId'		=> self::INT,
		'dormitoryName'		=> self::TEXT,
		'dormitoryAlias'	=> self::TEXT,
		'displayOrder'		=> self::INT,
		'active'		=> self::BOOL,
	);
	protected $tables = array(
		'' => 'dormitories',
	);
	protected $pk = 'id';
}
