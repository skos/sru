<?
/**
 * wyciagniecie pojedynczego przypisania admina Waleta do DSu
 */
class UFmap_SruWalet_AdminDormitory_Get
extends UFmap {

	protected $columns = array(
		'id'             => 'id',
		'admin'          => 'admin',
		'dormitory'      => 'dormitory',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'admin'          => self::INT,
		'dormitory'      => self::INT,
	);
	protected $tables = array(
		'' => 'admins_dormitories',
	);
	protected $pk = 'id';
}
