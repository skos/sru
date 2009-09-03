<?php
/**
 * dodanie usług usera
 */
class UFmap_Sru_UserService_Add
extends UFmap {

	protected $columns = array(
		'state'		=> 'active',
		'servType'	=> 'serv_type_id',
		'userId'	=> 'user_id'
	);
	protected $columnTypes = array(
		'state'		=> self::NULL_BOOL,
		'servType'	=> self::INT,
		'userId'	=> self::INT
	);
	protected $tables = array(
		'' => 'services'
	);
	protected $pk = 'id';
}

