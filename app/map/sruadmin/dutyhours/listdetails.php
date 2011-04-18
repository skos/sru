<?php
/**
 * wyciagniecie godzin dyzurow wraz ze szczegolami
 */
class UFmap_SruAdmin_DutyHours_ListDetails
extends UFmap_SruAdmin_DutyHours_List {

	protected $columns = array(
		'id'		=> 'd.id',
		'adminId'	=> 'd.admin_id',
		'day'		=> 'd.day',
		'startHour'	=> 'd.start_hour',
		'endHour'	=> 'd.end_hour',
		'active'	=> 'd.active',
		'comment'	=> 'd.comment',
		'adminName'	=> 'a.name',
		'adminAddress'	=> 'a.address',
		'adminEmail'	=> 'a.email',
		'adminDormId'	=> 'a.dormitory_id',
		'adminDormAlias'=> 'o.alias',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'adminId'	=> self::INT,
		'day'		=> self::INT,
		'startHour'	=> self::INT,
		'endHour'	=> self::INT,
		'active'	=> self::BOOL,
		'comment'	=> self::TEXT,
		'adminName'	=> self::TEXT,
		'adminAddress'	=> self::TEXT,
		'adminEmail'	=> self::TEXT,
		'adminDormId'	=> self::TEXT,
		'adminDormAlias'=> self::TEXT,
	);
	protected $tables = array(
		'd' => 'duty_hours',
	);
	protected $joins = array( 
		'a' => 'admins',
		'o' => 'dormitories',
	);
	protected $joinOns = array(
		'a' => 'd.admin_id=a.id',
		'o' => 'a.dormitory_id=o.id',
	);
	protected $pk = 'd.id';
}

