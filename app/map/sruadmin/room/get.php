<?php
/**
 * wyciagniecie pojedynczego pokoju
 */
class UFmap_SruAdmin_Room_Get
extends UFmap {

	protected $columns = array(
		'id'			=> 'l.id',
		'alias'			=> 'l.alias',
		'comment'		=> 'l.comment',	
		'userCount'		=> 'l.users_count',
		'computerCount' => 'l.computers_count',
		'dormitoryId'	=> 'l.dormitory_id',
		'dormitoryAlias'=> 'd.alias',
		'dormitoryName' => 'd.name',		
		
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
	);
	protected $tables = array(
		'l' => 'locations',
	);
	protected $joins = array(
		'd' => 'dormitories',
	);	
	protected $joinOns = array(
		'd' => 'l.dormitory_id=d.id',
	);	
	protected $pk = 'id';
}
