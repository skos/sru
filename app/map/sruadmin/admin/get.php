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
		'passwordInner'  => 'a.password_inner',
		'name'           => 'a.name',
		'lastLoginAt'    => 'a.last_login_at',
		'lastLoginIp'    => 'a.last_login_ip',
		'lastInvLoginAt' => 'a.last_inv_login_at',
		'lastInvLoginIp' => 'a.last_inv_login_ip',
		'typeId'         => 'a.type_id',
		'phone'          => 'a.phone',
		'jid'            => 'a.jid',
		'email'          => 'a.email',
		'address'        => 'a.address',
		'active'         => 'a.active',
		'dormitoryId'    => 'a.dormitory_id',
		'dormitoryAlias' => 'd.alias',
		'dormitoryName'  => 'd.name',
		'activeTo'	 => 'a.active_to',
		'modifiedById'   => 'a.modified_by',
		'modifiedByName' => 'b.name',
		'modifiedAt'     => 'a.modified_at',
		'lastPswChange'  => 'a.last_psw_change',
                'badLogins'	 => 'a.bad_logins',
		'lastPswInnerChange' => 'a.last_psw_inner_change',
		'displayOrder' => 'd.display_order'
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'login'          => self::TEXT,
		'password'       => self::TEXT,
		'passwordInner'  => self::TEXT,
		'name'           => self::TEXT,
		'lastLoginAt'    => self::NULL_TS,
		'lastLoginIp'    => self::NULL_TEXT,
		'lastInvLoginAt' => self::TS,
		'lastInvLoginIp' => self::TEXT,
		'typeId'         => self::INT,
		'phone'          => self::TEXT,
		'jid'            => self::TEXT,
		'email'          => self::TEXT,
		'address'        => self::TEXT,
		'active'         => self::BOOL,
		'dormitoryId'    => self::NULL_INT,
		'dormitoryAlias' => self::TEXT,
		'dormitoryName'  => self::TEXT,
		'activeTo'	 => self::NULL_TS,
		'modifiedById'   => self::NULL_INT,
		'modifiedByName' => self::TEXT,
		'modifiedAt'     => self::TS,
		'lastPswChange'  => self::TS,
		'badLogins'	 => self::INT,
		'lastPswInnerChange' => self::TS,
		'displayOrder' => self::INT
	);
	protected $tables = array(
		'a' => 'admins',
	);
	protected $joins = array(
		'd' => 'dormitories',
		'b' => 'admins',
	);
	protected $joinOns = array(
		'd' => 'a.dormitory_id=d.id',
		'b' => 'a.modified_by=b.id',
	);
	protected $pk = 'a.id';
}
