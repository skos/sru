<?php

/**
 * modyfikacja admina Waleta
 */

class UFmap_SruWalet_Admin_Set
extends UFmap {
	protected $columns = array(
		'password'       => 'password',
		'lastLoginAt'	 => 'last_login_at',
		'lastLoginIp'	 => 'last_login_ip',
		'lastInvLoginAt' => 'last_inv_login_at',
		'lastInvLoginIp' => 'last_inv_login_ip',
		'name'           => 'name',
		'typeId'         => 'type_id',
		'phone'          => 'phone',
		'jid'            => 'jid',
		'email'          => 'email',
		'address'        => 'address',
		'active'	 => 'active',
		'modifiedById'   => 'modified_by',
		'modifiedAt'     => 'modified_at',
		'lastPswChange'  => 'last_psw_change',
		'badLogins'	 => 'bad_logins'
	);
	protected $columnTypes = array(
		'password'       => self::TEXT,
		'lastLoginAt'	 => self::TS,
		'lastLoginIp'	 => self::TEXT,
		'lastInvLoginAt' => self::TS,
		'lastInvLoginIp' => self::TEXT,
		'name'           => self::TEXT,
		'typeId'         => self::INT,
		'phone'          => self::TEXT,
		'jid'            => self::TEXT,
		'email'          => self::TEXT,
		'address'        => self::TEXT,
		'active'	 => self::BOOL,
		'modifiedById'   => self::NULL_INT,
		'modifiedAt'     => self::TS,
		'lastPswChange'  => self::TS,
		'badLogins'	 => self::INT
	);	
	protected $tables = array(
		'' => 'admins',
	);
	protected $valids = array(
		'login' => array('textMin'=>1, 'textMax'=>100, 'regexp'=>'^[-a-zA-Z0-9\.@_]+$'),
		'password' => array('textMin'=>8, 'regexp'=>'^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$'),
		'name' => array('textMin'=>1, 'textMax'=>100),
		'email' => array('email'=>true),
		'lastLoginIp' => array('regexp'=>'^([0-9a-fA-F:]+)?[0-9.]{7,15}$'),
	);
	protected $pk = 'id';
}
