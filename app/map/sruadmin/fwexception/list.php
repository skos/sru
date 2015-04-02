<?php
/**
 * wyciagniecie wyjatkow w fw
 */
class UFmap_SruAdmin_FwException_List
extends UFmap {

	protected $columns = array(
		'id'			=> 'e.id',
		'computerId'		=> 'e.computer_id',
		'userId'		=> 'c.user_id',
		'userLogin'		=> 'u.login',
		'userName'		=> 'u.name',
		'userSurname'		=> 'u.surname',
		'host'			=> 'c.host',
		'ip'			=> 'c.ipv4',
		'port'			=> 'e.port',
		'active'		=> 'e.active',
		'waiting'		=> 'e.waiting',
		'applicationId'		=> 'e.fw_exception_application_id',
		'modifiedBy'		=> 'e.modified_by',
		'modifiedByName'	=> 'a.name',
		'modifiedAt'		=> 'e.modified_at',
	);
	protected $columnTypes = array(
		'id'			=> self::INT,
		'computerId'		=> self::INT,
	    	'userId'		=> self::INT,
		'userLogin'		=> self::TEXT,
		'userName'		=> self::TEXT,
		'userSurname'		=> self::TEXT,
		'host'			=> self::TEXT,
		'ip'			=> self::TEXT,
		'port'			=> self::INT,
		'active'		=> self::BOOL,
		'waiting'		=> self::BOOL,
		'applicationId'		=> self::NULL_INT,
	    	'modifiedBy'		=> self::NULL_INT,
		'modifiedByName'	=> self::NULL_TEXT,
		'modifiedAt'		=> self::TS,
	);
	protected $tables = array(
		'e' => 'fw_exceptions',
	);
	protected $joins = array(
		'c' => 'computers',
		'u' => 'users',
		'a' => 'admins',
	);
	protected $joinOns = array(
		'c' => 'e.computer_id=c.id',
		'u' => 'c.user_id=u.id',
		'a' => 'e.modified_by=a.id',
	);
	protected $pk = 'i.id';
}

