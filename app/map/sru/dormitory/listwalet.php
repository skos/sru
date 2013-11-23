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
		'display_order'		=> 'display_order',
	);
	protected $columnTypes = array(
		'dormitoryId'		=> self::INT,
		'dormitoryName'		=> self::TEXT,
		'dormitoryAlias'	=> self::TEXT,
		'display_order'		=> self::INT,
	);
	protected $tables = array(
		'' => 'dormitories',
	);
	protected $pk = 'id';
}
