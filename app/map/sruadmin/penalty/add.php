<?php
/**
 * dodanie kary
 */
class UFmap_SruAdmin_Penalty_Add
extends UFmap {
	protected $columns = array(
		'adminId'		=> 'admin_id',
		'userId'		=> 'user_id',
		'typeId' 		=> 'type_id',
		'startTime'		=> 'start_time',
		'endTime'		=> 'end_time',
		'comment'		=> 'comment',
		'modifiedBy'	=> 'modified_by',
		'modifiedAt'	=> 'modified_at',
		'active'		=> 'active',
		'reasonId'		=> 'reason_id',
	);
	protected $columnTypes = array(
		'adminId'		=> self::INT,
		'userId'		=> self::INT,
		'typeId' 		=> self::INT,
		'startTime'		=> self::TS,
		'endTime'		=> self::TS,
		'comment'		=> self::TEXT,
		'modifiedBy'	=> self::NULL_INT, 
		'modifiedAt'	=> self::TS,
		'active'		=> self::BOOL,	
		'reasonId'		=> self::INT,
	);	

	protected $tables = array(
		'' => 'users_penalties',
	);
	protected $valids = array(

		'typeId'   => array('textMin'=>1),
		'reasonId' => array('textMin'=>1),
		//@todo: validacja dat?
	);
	protected $pk = 'id';
}
