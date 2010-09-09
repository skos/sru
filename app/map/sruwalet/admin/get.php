<?
/**
 * wyciagniecie pojedynczego admina Waleta
 */
class UFmap_SruWalet_Admin_Get
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
		'gg'             => 'a.gg',
		'jid'            => 'a.jid',
		'email'          => 'a.email',
		'address'        => 'a.address',
		'active'         => 'a.active',
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
	);
	protected $tables = array(
		'a' => 'admins',
	);
	protected $pk = 'a.id';
}
