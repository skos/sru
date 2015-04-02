<?php
/**
 * wyciagniecie godzin dyzurow
 */
class UFmap_SruAdmin_DutyHours_Get
extends UFmap {

	protected $columns = array(
		'id'		=> 'd.id',
		'adminId'	=> 'd.admin_id',
		'day'		=> 'd.day',
		'startHour'	=> 'd.start_hour',
		'endHour'	=> 'd.end_hour',
		'active'	=> 'd.active',
		'comment'	=> 'd.comment',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'adminId'	=> self::INT,
		'day'		=> self::INT,
		'startHour'	=> self::INT,
		'endHour'	=> self::INT,
		'active'	=> self::BOOL,
		'comment'	=> self::TEXT,
	);
	protected $tables = array(
		'd' => 'duty_hours',
	);
	protected $joins = array( 
	);
	protected $joinOns = array(
	);
	protected $pk = 'd.id';
}

