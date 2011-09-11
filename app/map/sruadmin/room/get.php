<?php
/**
 * wyciagniecie pojedynczego pokoju
 */
class UFmap_SruAdmin_Room_Get
extends UFmap {

	protected $columns = array(
		'id'		=> 'l.id',
		'alias'		=> 'l.alias',
		'comment'	=> 'l.comment',	
		'userCount'	=> 'l.users_count',
		'computerCount' => 'l.computers_count',
		'dormitoryId'	=> 'l.dormitory_id',
		'dormitoryAlias'=> 'd.alias',
		'dormitoryName' => 'd.name',
		'usersMax'	=> 'l.users_max',
		'modifiedById'   => 'l.modified_by',
		'modifiedByName' => 'a.name',
		'modifiedAt'     => 'l.modified_at',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'alias'          => self::TEXT,
		'comment'           => self::TEXT,
		'userCount'     => self::INT,
		'computerCount' => self::INT,
		'dormitoryId'    => self::INT,
		'dormitoryAlias' => self::TEXT,
		'dormitoryName'  => self::TEXT,
		'usersMax'	 => self::INT,
		'modifiedById'   => self::NULL_INT,
		'modifiedByName' => self::TEXT,
		'modifiedAt'     => self::TS,
	);
	protected $tables = array(
		'l' => 'locations',
	);
	protected $joins = array(
		'd' => 'dormitories',
		'a' => 'admins',
	);	
	protected $joinOns = array(
		'd' => 'l.dormitory_id=d.id',
		'a' => 'l.modified_by=a.id',
	);	
	protected $pk = 'id';
}
