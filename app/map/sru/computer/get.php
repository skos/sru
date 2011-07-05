<?
/**
 * wybranie pojedynczego komputera
 */
class UFmap_Sru_Computer_Get
extends UFmap {

	protected $columns = array(
		'id'             => 'c.id',
		'host'           => 'c.host',
		'mac'            => 'c.mac',
		'ip'             => 'c.ipv4',
		'userId'         => 'c.user_id',
		'userName'       => 'u.name',
		'userSurname'    => 'u.surname',
		'userComment'	 => 'u.comment',
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
		'lastSeen'		 => 'c.last_seen',
		'lastActivated'	 => 'c.last_activated',
		'locationComment'=> 'l.comment',
		'carerId'		 => 'c.carer_id',
		'carerName'		 => 'w.name',
		'masterHostId'	 => 'c.master_host_id',
		'masterHostName' => 's.host',
		'autoDeactivation' => 'c.auto_deactivation',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'host'           => self::TEXT,
		'mac'            => self::TEXT,
		'ip'             => self::TEXT,
		'userId'         => self::NULL_INT,
		'userName'       => self::TEXT,
		'userSurname'    => self::TEXT,
		'userComment'	 => self::TEXT,
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
		'lastSeen'		 => self::TS,
		'lastActivated'	 => self::TS,
		'locationComment'=> self::TEXT,
		'carerId'		 => self::NULL_INT,
		'carerName'		 => self::TEXT,
		'masterHostId'	 => self::NULL_INT,
		'masterHostName' => self::TEXT,
		'autoDeactivation' => self::BOOL,
	);
	protected $tables = array(
		'c' => 'computers',
	);
	protected $joins = array(
		'u' => 'users',
		'a' => 'admins',
		'l' => 'locations',
		'd' => 'dormitories',
		'w' => 'admins',
		's' => 'computers',
	);
	protected $joinOns = array(
		'u' => 'c.user_id=u.id',
		'a' => 'c.modified_by=a.id',
		'l' => 'c.location_id=l.id',
		'd' => 'l.dormitory_id=d.id',
		'w' => 'c.carer_id=w.id',
		's' => 'c.master_host_id=s.id',
	);
	protected $valids = array(
		'host' => array('textMin'=>1, 'textMax'=>50, 'regexp'=>'^[a-z][-a-z0-9]*$'),
		'mac' => array('regexp'=>'^[0-9a-f]{1,2}?([- :]?[0-9a-f]{1,2}){5}$'),
		'ip' => array('regexp'=>'^[0-9.]{7,15}$'),
		'locationId' => array('textMin'=>1),
	);
	protected $pk = 'id';
}
