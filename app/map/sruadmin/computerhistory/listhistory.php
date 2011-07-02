<?
/**
 * wybranie listy zmian danych komputerow
 */
class UFmap_SruAdmin_ComputerHistory_ListHistory
extends UFmap {

	protected $columns = array(
		'id'             => 'h.id',
		'computerId'     => 'h.computer_id',
		'host'           => 'h.host',
		'mac'            => 'h.mac',
		'ip'             => 'h.ipv4',
		'userId'         => 'h.user_id',
		'userName'       => 'u.name',
		'userSurname'    => 'u.surname',
		'locationId'     => 'h.location_id',
		'availableTo'    => 'h.avail_to',
		'modifiedById'   => 'h.modified_by',
		'modifiedAt'     => 'h.modified_at',
		'comment'        => 'h.comment',
		'canAdmin'       => 'h.can_admin',
		'exAdmin'        => 'h.exadmin',
		'active'         => 'h.active',
		'typeId'	 => 'h.type_id',
		'currentBanned'	 => 'c.banned',
		'currentActive'	 => 'c.active',
		'currentComment' => 'c.comment',
		'currentIp'      => 'c.ipv4',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'computerId'     => self::INT,
		'host'           => self::TEXT,
		'mac'            => self::TEXT,
		'ip'             => self::TEXT,
		'userId'         => self::NULL_INT,
		'userName'       => self::TEXT,
		'userSurname'    => self::TEXT,
		'locationId'     => self::INT,
		'availableTo'    => self::NULL_TS,
		'modifiedById'   => self::NULL_INT,
		'modifiedAt'     => self::TS,
		'comment'        => self::TEXT,
		'canAdmin'       => self::BOOL,
		'exAdmin'        => self::BOOL,
		'active'         => self::BOOL,
		'typeId'	 => self::INT,
		'currentBanned'  => self::BOOL,
		'currentActive'  => self::BOOL,
		'currentComment' => self::TEXT,
		'currentIp'      => self::TEXT,
	);
	protected $tables = array(
		'h' => 'computers_history',
	);
	protected $joins = array(
		'c' => 'computers',
		'u' => 'users',
	);
	protected $joinOns = array(
		'c' => 'h.computer_id=c.id',
		'u' => 'h.user_id=u.id',
	);
	protected $pk = 'h.id';
}
