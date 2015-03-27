<?php
/**
 * dodanie funkcji usera
 */
class UFmap_Sru_UserFunction_Add
extends UFmap {
	protected $columns = array(
		'id'		=> 'id',
		'userId'	=> 'user_id',
		'functionId'	=> 'function_id',
		'dormitoryId'	=> 'dormitory_id',
		'comment'	=> 'comment',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'userId'	=> self::INT,
		'functionId'	=> self::INT,
		'dormitoryId'	=> self::NULL_INT,
		'comment'	=> self::TEXT,
	);
	protected $tables = array(
		'' => 'users_functions',
	);

	protected $valids = array(
		'comment' => array('textMax'=>64),
	);
	
	protected $pk = 'id';
}

