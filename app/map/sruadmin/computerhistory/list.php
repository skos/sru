<?
/**
 * wybranie listy zmian danych komputerow
 */
class UFmap_SruAdmin_ComputerHistory_List
extends UFmap {

	protected $columns = array(
		'id'             => 'h.id',
		'computerId'     => 'h.computer_id',
		'host'           => 'h.host',
		'mac'            => 'h.mac',
		'ip'             => 'h.ipv4',
		'userId'         => 'h.user_id',
		'locationId'     => 'h.location_id',
		'locationAlias'  => 'l.alias',
		'dormitoryId'    => 'l.dormitory_id',
		'dormitoryAlias' => 'd.alias',
		'dormitoryName'  => 'd.name',
		'availableTo'    => 'h.avail_to',
		'availableMaxTo' => 'h.avail_max_to',
		'modifiedById'   => 'h.modified_by',
		'modifiedBy'     => 'a.name',
		'modifiedAt'     => 'h.modified_at',
		'comment'        => 'h.comment',
		'canAdmin'       => 'h.can_admin',
		'active'         => 'h.active',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'computerId'     => self::INT,
		'host'           => self::TEXT,
		'mac'            => self::TEXT,
		'ip'             => self::TEXT,
		'userId'         => self::NULL_INT,
		'locationId'     => self::INT,
		'locationAlias'  => self::TEXT,
		'dormitoryId'    => self::INT,
		'dormitoryAlias' => self::TEXT,
		'dormitoryName'  => self::TEXT,
		'availableTo'    => self::TS,
		'availableMaxTo' => self::TS,
		'modifiedById'   => self::NULL_INT,
		'modifiedBy'     => self::TEXT,
		'modifiedAt'     => self::TS,
		'comment'        => self::TEXT,
		'canAdmin'       => self::BOOL,
		'active'         => self::BOOL,
	);
	protected $tables = array(
		'h' => 'computers_history',
	);
	protected $joins = array(
		'a' => 'admins',
		'l' => 'locations',
		'd' => 'dormitories',
	);
	protected $joinOns = array(
		'a' => 'h.modified_by=a.id',
		'l' => 'h.location_id=l.id',
		'd' => 'l.dormitory_id=d.id',
	);
	protected $pk = 'h.id';
}
