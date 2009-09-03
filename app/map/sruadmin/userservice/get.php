<?php
/**
 * ustawianie usług usera
 */
class UFmap_SruAdmin_UserService_Get
extends UFmap {

	protected $columns = array(
		'id'		=> 'id',
		'state'		=> 'active',
		'servType'	=> 'serv_type_id',
		'userId'	=> 'user_id',
		'modifiedById'	=> 'modified_by'
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'state'		=> self::NULL_BOOL,
		'servType'	=> self::INT,
		'userId'	=> self::INT,
		'modifiedById'	=> self::NULL_INT
	);
	protected $tables = array(
		'' => 'services'
	);
	protected $pk = 'id';
}

