<?php
/**
 * wyciagniecie pojedynczego akademika
 */
class UFmap_SruAdmin_Dorm_Get
extends UFmap {

	protected $columns = array(
		'id'             => 'd.id',
		'name'         	 => 'd.name',
		'alias'      	 => 'd.alias',
		'users_count'    => 'd.users_count',
		'computers_count'=> 'd.computers_count',
	);
	protected $columnTypes = array(
		'id'             =>  self::INT,
		'name'         	 =>  self::TEXT,
		'alias'      	 =>  self::TEXT,
		'users_count'    =>  self::INT,
		'computers_count'=>  self::INT,
	);
	protected $tables = array(
		'd' => 'dormitories',
	);
	protected $pk = 'd.id';
}
