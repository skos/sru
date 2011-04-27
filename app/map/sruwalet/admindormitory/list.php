<?
/**
 * wyciagniecie listy przypisan admina Waleta do DSu
 */
class UFmap_SruWalet_AdminDormitory_List
extends UFmap {

	protected $columns = array(
		'id'             => 'a.id',
		'admin'          => 'a.admin',
		'adminName'	 => 'a.name',
		'dormitory'      => 'a.dormitory',
		'dormitoryName'  => 'd.name',
		'dormitoryAlias' => 'd.alias',
		'dormitoryId'	 => 'd.id',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'admin'          => self::INT,
		'adminName'      => self::TEXT,
		'dormitory'      => self::INT,
		'dormitoryName'  => self::TEXT,
		'dormitoryAlias' => self::TEXT,
		'dormitoryId'	 => self::INT,
	);
	protected $tables = array(
		'a' => 'admins_dormitories',
	);
	protected $joins = array(
		'd' => 'dormitories',
	);
	protected $joinOns = array(
		'd' => 'a.dormitory = d.id',
	);
	protected $pk = 'a.id';
}
