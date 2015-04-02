<?php
/**
 * wyciagniecie godzin dyzurow wraz z godzinami dyżurów
 */
class UFmap_SruAdmin_DutyHours_ListDutyHours
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
		'adminActive'	=> 'a.active',
		'adminDormAlias'=> 'o.alias',
		'dutyDormId'	=> 'm.dormitory',
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
		'adminActive'	=> self::BOOL,
		'adminDormAlias'=> self::TEXT,
		'dutyDormId'	=> self::INT,
	);
	protected $tables = array(
		'd' => 'duty_hours',
	);
	protected $joins = array( 
		'a' => 'admins',
		'o' => 'dormitories',
		'm' => 'admins_dormitories',
	);
	protected $joinOns = array(
		'a' => 'd.admin_id=a.id',
		'o' => 'a.dormitory_id=o.id',
		'm' => 'd.admin_id=m.admin',
	);
	protected $pk = 'd.id';
}

