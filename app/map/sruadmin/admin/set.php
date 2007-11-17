<?php

/**
 * modyfikacja admina
 */
//@todo: why is it needed for adding? 
class UFmap_SruAdmin_Admin_Set
extends UFmap {
	protected $columns = array(
		'login'          => 'login',
		'password'       => 'password',
		'name'           => 'name',
		'lastLoginAt'    => 'last_login_at',
		'lastLoginIp'    => 'last_login_ip',
		'typeId'         => 'type_id',
		'phone'          => 'phone',
		'gg'             => 'gg',
		'jid'            => 'jid',
		'email'          => 'email',
		'address'        => 'address',
		'dormitoryId'    => 'dormitory_id',
	);
	protected $columnTypes = array(
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
		'dormitoryId'    => self::NULL_INT,
	);	
	protected $tables = array(
		'' => 'admins',
	);
	protected $valids = array(
//		'login' => array('textMin'=>1, 'textMax'=>100, 'regexp'=>'^[-a-zA-Z0-9\.@_]+$'),
//		'password' => array('textMin'=>6), 
//		'name' => array('textMin'=>1, 'textMax'=>100, 'regexp'=>'^[-a-zA-Z ]+$'),
//		'email' => array('email'=>true),
//		'dormitory' => array('textMin'=>1),
	);
	protected $pk = 'id';
}
