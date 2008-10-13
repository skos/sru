<?php
/**
 * dodanie admina
 */
class UFmap_SruAdmin_Admin_Add
extends UFmap {
	protected $columns = array(
		'login'          => 'login',
		'password'       => 'password',
		'name'           => 'name',
		'typeId'         => 'type_id',
		'phone'          => 'phone',
		'gg'             => 'gg',
		'jid'            => 'jid',
		'email'          => 'email',
		'address'        => 'address',
		'dormitoryId'  	 => 'dormitory_id',
	);
	protected $columnTypes = array(
		'login'          => self::TEXT,
		'password'       => self::TEXT,
		'name'           => self::TEXT,
		'typeId'         => self::INT,
		'phone'          => self::TEXT,
		'gg'             => self::TEXT,
		'jid'            => self::TEXT,
		'email'          => self::TEXT,
		'address'        => self::TEXT,
		'dormitoryId'	 => self::NULL_INT,
	);	

	protected $tables = array(
		'' => 'admins',
	);
	protected $valids = array(
		'login' => array('textMin'=>1, 'textMax'=>100, 'regexp'=>'^[-a-zA-Z0-9\.@_]+$'),
		'password' => array('textMin'=>6), 
		'name' => array('textMin'=>1),
		'email' => array('email'=>true),
		'dormitoryId' => array('textMin'=>1),
		'typeId' => array('textMin'=>1), //@todo: a takie typy jakos dokladniej nei powinien byc validowane?
	);
	protected $pk = 'id';
}
