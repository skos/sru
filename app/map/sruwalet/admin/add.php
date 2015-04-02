<?php
/**
 * dodanie admina Waleta
 */
class UFmap_SruWalet_Admin_Add
extends UFmap {
	protected $columns = array(
		'login'          => 'login',
		'password'       => 'password',
		'name'           => 'name',
		'typeId'         => 'type_id',
		'phone'          => 'phone',
		'jid'            => 'jid',
		'email'          => 'email',
		'address'        => 'address',
		'modifiedById'   => 'modified_by',
		'activeTo'	 => 'active_to',
	);
	protected $columnTypes = array(
		'login'          => self::TEXT,
		'password'       => self::TEXT,
		'name'           => self::TEXT,
		'typeId'         => self::INT,
		'phone'          => self::TEXT,
		'jid'            => self::TEXT,
		'email'          => self::TEXT,
		'address'        => self::TEXT,
		'modifiedById'   => self::NULL_INT,
		'activeTo'	 => self::NULL_TS,
	);	

	protected $tables = array(
		'' => 'admins',
	);
	protected $valids = array(
		'login' => array('textMin'=>1, 'textMax'=>100, 'regexp'=>'^[-a-zA-Z0-9\.@_]+$'),
		'password' => array('textMin'=>8, 'regexp'=>'^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$'),  
		'name' => array('textMin'=>1, 'textMax'=>100),
		'email' => array('email'=>true, 'textMax'=>100),
		'typeId' => array('textMin'=>1), //@todo: a takie typy jakos dokladniej nei powinien byc validowane?
	);
	protected $pk = 'id';
}
