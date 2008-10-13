<?php

/**
 * modyfikacja admina
 */

class UFmap_SruAdmin_Admin_Set
extends UFmap {
	protected $columns = array(
		'password'       => 'password',
		'lastLoginAt'	 => 'last_login_at',
		'lastLoginIp'	 => 'last_login_ip',
		'name'           => 'name',
		'typeId'         => 'type_id',
		'phone'          => 'phone',
		'gg'             => 'gg',
		'jid'            => 'jid',
		'email'          => 'email',
		'address'        => 'address',
		'active'		 => 'active',
		'dormitoryId'    => 'dormitory_id',
	);
	protected $columnTypes = array(
		'password'       => self::TEXT,
		'lastLoginAt'	 => self::TS,
		'lastLoginIp'	 => self::TEXT,
		'name'           => self::TEXT,
		'typeId'         => self::INT,
		'phone'          => self::TEXT,
		'gg'             => self::TEXT,
		'jid'            => self::TEXT,
		'email'          => self::TEXT,
		'address'        => self::TEXT,
		'active'		 => self::BOOL,
		'dormitoryId'    => self::NULL_INT,
	);	
	protected $tables = array(
		'' => 'admins',
	);
	protected $valids = array(
		'login' => array('textMin'=>1, 'textMax'=>100, 'regexp'=>'^[-a-zA-Z0-9\.@_]+$'),
		'password' => array('textMin'=>6), 
		'name' => array('textMin'=>1, 'textMax'=>100),
		'email' => array('email'=>true),
		'dormitory' => array('textMin'=>1),
		'lastLoginIp' => array('regexp'=>'^([0-9a-fA-F:]+)?[0-9.]{7,15}$'),
	);
	protected $pk = 'id';
}
