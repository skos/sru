<?
/**
 * wybranie listy komputerow
 */
class UFmap_Sru_Computer_List
extends UFmap {

	protected $columns = array(
		'id'             => 'c.id',
		'host'           => 'c.host',
		'mac'            => 'c.mac',
		'ip'             => 'c.ipv4',
		'userId'         => 'c.user_id',
		'userName'       => 'u.name',
		'userSurname'    => 'u.surname',
		'login'			 => 'u.login',
		'userActive'	 => 'u.active',
		'userTypeId'	 => 'u.type_id',
		'locationId'     => 'c.location_id',
		'locationAlias'  => 'l.alias',
		'dormitoryId'    => 'l.dormitory_id',
		'dormitoryAlias' => 'd.alias',
		'dormitoryName'  => 'd.name',
		'availableTo'    => 'c.avail_to',
		'modifiedById'   => 'c.modified_by',
		'modifiedBy'     => 'a.name',
		'modifiedAt'     => 'c.modified_at',
		'comment'        => 'c.comment',
		'active'         => 'c.active',
		'typeId'         => 'c.type_id',
		'bans'           => 'c.bans',
		'banned'         => 'c.banned',
		'canAdmin'       => 'c.can_admin',
		'exAdmin'        => 'c.exadmin',
		'carerId'		 => 'c.carer_id',
		'masterHostId'	 => 'c.master_host_id',
		'lastSeen'		 => 'c.last_seen',
		'lastActivated'	 => 'c.last_activated',
		'autoDeactivation' => 'c.auto_deactivation',
		'domainName' => 'c.domain_name',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'host'           => self::TEXT,
		'mac'            => self::TEXT,
		'ip'             => self::TEXT,
		'userId'         => self::NULL_INT,
		'userName'       => self::TEXT,
		'userSurname'    => self::TEXT,
		'login'			 => self::TEXT,
		'userActive'	 => self::BOOL,
		'userTypeId'	 => self::INT,
		'locationId'     => self::INT,
		'locationAlias'  => self::TEXT,
		'dormitoryId'    => self::INT,
		'dormitoryAlias' => self::TEXT,
		'dormitoryName'  => self::TEXT,
		'availableTo'    => self::NULL_TS,
		'modifiedById'   => self::NULL_INT,
		'modifiedBy'     => self::TEXT,
		'modifiedAt'     => self::TS,
		'comment'        => self::TEXT,
		'active'         => self::BOOL,
		'typeId'         => self::INT,
		'bans'           => self::INT,
		'banned'         => self::BOOL,
		'canAdmin'       => self::BOOL,
		'exAdmin'        => self::BOOL,
		'carerId'		 => self::NULL_INT,
		'masterHostId'	 => self::NULL_INT,
		'lastSeen'		 => self::TS,
		'lastActivated'	 => self::TS,
		'autoDeactivation' => self::BOOL,
		'domainName' => self::TEXT,
	);
	protected $tables = array(
		'c' => 'computers',
	);
	protected $joins = array(
		'u' => 'users',
		'a' => 'admins',
		'l' => 'locations',
		'd' => 'dormitories',
	);
	protected $joinOns = array(
		'u' => 'c.user_id=u.id',
		'a' => 'c.modified_by=a.id',
		'l' => 'c.location_id=l.id',
		'd' => 'l.dormitory_id=d.id',
	);
	protected $pk = 'id';
}
