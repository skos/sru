<?php
/**
 * dodanie admina
 */
class UFmap_SruAdmin_Admin_Add
extends UFmap {
	protected $columns = array(
		'login'          => 'login',
		'password'       => 'password',
		'passwordInner'  => 'password_inner', // boty mają na starcie to hasło
		'name'           => 'name',
		'typeId'         => 'type_id',
		'phone'          => 'phone',
		'jid'            => 'jid',
		'email'          => 'email',
		'address'        => 'address',
		'dormitoryId'  	 => 'dormitory_id',
		'activeTo'		 => 'active_to',
		'modifiedById'   => 'modified_by',
		'computerId'	 => 'id',
	);
	protected $columnTypes = array(
		'login'          => self::TEXT,
		'password'       => self::TEXT,
		'passwordInner'  => self::TEXT,
		'name'           => self::TEXT,
		'typeId'         => self::INT,
		'phone'          => self::TEXT,
		'jid'            => self::TEXT,
		'email'          => self::TEXT,
		'address'        => self::TEXT,
		'dormitoryId'	 => self::NULL_INT,
		'activeTo'		 => self::NULL_TS,
		'modifiedById'   => self::NULL_INT,
		'computerId'	 => self::INT,
	);	

	protected $tables = array(
		'' => 'admins',
	);
	protected $valids = array(
		'login' => array('textMin'=>1, 'textMax'=>100, 'regexp'=>'^[-a-zA-Z0-9\.@_]+$'),
		'password' => array('textMin'=>8, 'regexp'=>'^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).*$'), 
		'name' => array('textMin'=>1, 'textMax'=>100),
		'email' => array('email'=>true, 'textMax'=>100),
		'dormitoryId' => array('textMin'=>1),
		'typeId' => array('textMin'=>1), //@todo: a takie typy jakos dokladniej nei powinien byc validowane?
	);
	protected $pk = 'id';
}
