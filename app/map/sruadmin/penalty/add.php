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
		'startAt'		=> 'start_at',
		'endAt'			=> 'end_at',
		'comment'		=> 'comment',
		'modifiedBy'	=> 'modified_by',
		'modifiedAt'	=> 'modified_at',		
		'amnestyAt'		=> 'amnesty_at',
		'amnestyAfter'	=> 'amnesty_after',
		'amnestyBy'		=> 'amnesty_by',
		'createdAt'		=> 'created_at',
		'reason'		=> 'reason',
	);
	protected $columnTypes = array(
		'adminId'		=> self::INT,
		'userId'		=> self::INT,
		'typeId' 		=> self::INT,
		'startAt'		=> self::TS,
		'endAt'			=> self::TS,
		'comment'		=> self::TEXT,
		'modifiedBy'	=> self::NULL_INT, 
		'modifiedAt'	=> self::TS,		
		'amnestyAt'		=> self::NULL_TS,
		'amnestyAfter'	=> self::NULL_TS,
		'amnestyBy'		=> self::INT,
		'createdAt'		=> self::TS,		
		'reason'		=> self::TEXT,
	);	

	protected $tables = array(
		'' => 'penalties',
	);
	protected $valids = array(

		'typeId'   => array('textMin'=>1),
		'reasonId' => array('textMin'=>1),
		//@todo: validacja dat?
	);
	protected $pk = 'id';
}
