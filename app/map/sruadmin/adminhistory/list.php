<?
/**
 * wybranie listy zmian danych admina
 */
class UFmap_SruAdmin_AdminHistory_List
extends UFmap {

	protected $columns = array(
		'id'             => 'h.id',
		'adminId'		 => 'h.admin_id',
		'login'          => 'h.login',
		'name'           => 'h.name',
		'typeId'         => 'h.type_id',
		'phone'          => 'h.phone',
		'jid'            => 'h.jid',
		'email'          => 'h.email',
		'address'        => 'h.address',
		'active'         => 'h.active',
		'activeTo'		 => 'h.active_to',
		'dormitoryId'    => 'h.dormitory_id',
		'dormitoryAlias' => 'd.alias',
		'dormitoryName'  => 'd.name',
		'modifiedById'   => 'h.modified_by',
		'modifiedByName' => 'a.name',
		'modifiedAt'     => 'h.modified_at',
		'passwordChanged'=> 'h.password_changed',
		'passwordInnerChanged'=> 'h.password_inner_changed',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'adminId'		 => self::INT,
		'login'          => self::TEXT,
		'name'           => self::TEXT,
		'typeId'         => self::INT,
		'phone'          => self::TEXT,
		'jid'            => self::TEXT,
		'email'          => self::TEXT,
		'address'        => self::TEXT,
		'active'         => self::BOOL,
		'dormitoryId'    => self::NULL_INT,
		'dormitoryAlias' => self::TEXT,
		'dormitoryName'  => self::TEXT,
		'activeTo'		 => self::NULL_TS,
		'modifiedById'   => self::NULL_INT,
		'modifiedByName' => self::TEXT,
		'modifiedAt'     => self::TS,
		'passwordChanged'=> self::TS,
		'passwordInnerChanged'=> self::TS,
	);
	protected $tables = array(
		'h' => 'admins_history',
	);
	protected $joins = array(
		'd' => 'dormitories',
		'a' => 'admins',
	);
	protected $joinOns = array(
		'd' => 'h.dormitory_id=d.id',
		'a' => 'h.modified_by=a.id',
	);
	protected $pk = 'h.id';
}
