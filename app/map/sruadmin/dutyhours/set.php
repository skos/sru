<?php
/**
 * zapisanie godzin dyzurow
 */
class UFmap_SruAdmin_DutyHours_Set
extends UFmap {

	protected $columns = array(
		'id'		=> 'id',
		'adminId'	=> 'admin_id',
		'day'		=> 'day',
		'startHour'	=> 'start_hour',
		'endHour'	=> 'end_hour',
		'active'	=> 'active',
		'comment'	=> 'comment',
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
		'' => 'duty_hours',
	);
	protected $valids = array(
		'startHour' => array('textMin'=>3, 'textMax'=>4, 'regexp'=>'^[0-2]?[0-9]+[0-6]+[0-9]+$'),
		'endHour' => array('textMin'=>3, 'textMax'=>4, 'regexp'=>'^[0-2]?[0-9]+[0-6]+[0-9]+$'),
	);
	protected $pk = 'id';
}

