<?php
/**
 * pobranie funkcji usera
 */
class UFmap_Sru_UserFunction_Get
extends UFmap {

	protected $columns = array(
		'id'			=> 'f.id',
		'userId'		=> 'f.user_id',
		'userName'		=> 'u.name',
		'userSurname'		=> 'u.surname',
		'userEmail'		=> 'u.email',
		'functionId'		=> 'f.function_id',
		'dormitoryId'		=> 'f.dormitory_id',
		'comment'		=> 'f.comment',
	);
	protected $columnTypes = array(
		'id'			=> self::INT,
		'userId'		=> self::INT,
		'userName'		=> self::TEXT,
		'userSurname'		=> self::TEXT,
		'userEmail'		=> self::TEXT,
		'functionId'		=> self::INT,
		'dormitoryId'		=> self::NULL_INT,
		'comment'		=> self::TEXT,
	);
	protected $tables = array(
		'f' => 'users_functions',
	);
	protected $joins = array(
		'u' => 'users',
	);
	protected $joinOns = array(
		'u' => 'f.user_id=u.id',
	);
	protected $pk = 'f.id';
}

