<?
/**
 * wyciagniecie pojedynczej danej z waleta
 */
class UFmap_Sru_User_Walet
extends UFmap {

	protected $columns = array(
		'hash'         => 'hash',
		'room'         => 'room',
		'dormitory'    => 'dorm',
	);
	protected $columnTypes = array(
		'hash'         => self::TEXT,
		'room'         => self::TEXT,
		'dormitory'    => self::INT,
	);
	protected $tables = array(
		'' => 'users_walet',
	);
	protected $pk = 'id';
}
