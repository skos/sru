<?
/**
 * wyciagniecie pojedynczego admina
 */
class UFmap_SruAdmin_Admin_Get
extends UFmap {

	protected $columns = array(
		'id'             => 'a.id',
		'login'          => 'a.login',
		'password'       => 'a.password',
		'name'           => 'a.name',
		'lastLoginAt'    => 'a.last_login_at',
		'lastLoginIp'    => 'a.last_login_ip',
		'typeId'         => 'a.type_id',
		'phone'          => 'a.phone',
		'gg'             => 'a.phone',
		'jid'            => 'a.jid',
		'email'          => 'a.email',
		'address'        => 'a.address',
		'active'         => 'a.active',
		'dormitoryId'    => 'a.dormitory_id',
		'dormitoryAlias' => 'd.alias',
		'dormitoryName'  => 'd.name',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'login'          => self::TEXT,
		'password'       => self::TEXT,
		'name'           => self::TEXT,
		'lastLoginAt'    => self::NULL_TS,
		'lastLoginIp'    => self::NULL_TEXT,
		'typeId'         => self::INT,
		'phone'          => self::TEXT,
		'gg'             => self::TEXT,
		'jid'            => self::TEXT,
		'email'          => self::TEXT,
		'address'        => self::TEXT,
		'active'         => self::BOOL,
		'dormitoryId'    => self::NULL_INT,
		'dormitoryAlias' => self::TEXT,
		'dormitoryName'  => self::TEXT,
	);
	protected $tables = array(
		'a' => 'admins',
	);
	protected $joins = array(
		'd' => 'dormitories',
	);
	protected $joinOns = array(
		'd' => 'a.dormitory_id=d.id',
	);
	protected $pk = 'a.id';
}
