<?php
/**
 * wyciagniecie usług usera
 */
class UFmap_Sru_UserService_List
extends UFmap {

	protected $columns = array(
		'id'		=> 's.id',
		'state'		=> 's.active',
		'servType'	=> 's.serv_type_id',
		'userId'	=> 'u.id'
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'state'		=> self::NULL_BOOL,
		'servType'	=> self::INT,
		'userId'	=> self::INT
	);
	protected $tables = array(
		's' => 'services'
	);
	protected $joins = array(
		'u' => 'users'
	);
	protected $joinOns = array(
		'u' => 's.user_id=u.id'
	);
	protected $pk = 's.id';
}

