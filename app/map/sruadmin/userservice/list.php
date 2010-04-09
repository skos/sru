<?php
/**
 * wyciagniecie usług usera
 */
class UFmap_SruAdmin_UserService_List
extends UFmap {

	protected $columns = array(
		'id'		=> 's.id',
		'state'		=> 's.active',
		'servType'	=> 's.serv_type_id',
		'userId'	=> 'u.id',
		'userName'	=> 'u.name',
		'userSurname'	=> 'u.surname',
		'userLogin'	=> 'u.login',
		'userEmail'	=> 'u.email',
		'servName'	=> 't.name'

	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'state'		=> self::NULL_BOOL,
		'servType'	=> self::INT,
		'userId'	=> self::INT,
		'userName'	=> self::TEXT,
		'userSurname'	=> self::TEXT,
		'userLogin'	=> self::TEXT,
		'userEmail'	=> self::TEXT,
		'servName'	=> self::TEXT
	);
	protected $tables = array(
		's' => 'services'
	);
	protected $joins = array(
		't' => 'services_type',
		'u' => 'users'
	);
	protected $joinOns = array(
		't' => 's.serv_type_id=t.id',
		'u' => 's.user_id=u.id'
	);
	protected $pk = 's.id';
}

