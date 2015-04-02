<?
/**
 * wybranie listy zmian danych pokoju
 */
class UFmap_SruAdmin_RoomHistory_List
extends UFmap {

	protected $columns = array(
		'id'            => 'h.id',
		'locationId'	=> 'h.location_id',
		'comment'		=> 'h.comment',
		'modifiedById'  => 'h.modified_by',
		'modifiedByName'=> 'a.name',
		'modifiedAt'    => 'h.modified_at',
		'usersMax'		=> 'h.users_max',
		'typeId'		=> 'h.type_id',
	);
	protected $columnTypes = array(
		'id'            => self::INT,
		'locationId'	=> self::INT,
		'comment'		=> self::TEXT,
		'modifiedById'   => self::NULL_INT,
		'modifiedByName' => self::TEXT,
		'modifiedAt'     => self::TS,
		'usersMax'		=> self::INT,
		'typeId'		=> self::INT,
	);
	protected $tables = array(
		'h' => 'locations_history',
	);
	protected $joins = array(
		'a' => 'admins',
	);
	protected $joinOns = array(
		'a' => 'h.modified_by=a.id',
	);
	protected $pk = 'h.id';
}
