<?php
/**
 * wyciagniecie pojedynczej kary
 */
class UFmap_SruAdmin_Penalty_Get
extends UFmap {

	protected $columns = array(
		'id'			=> 'b.id',
		'adminId'		=> 'b.admin_id',
		'userId'		=> 'b.user_id',
		'typeId' 		=> 'b.type_id',
		'startTime'		=> 'b.start_time',
		'endTime'		=> 'b.end_time',
		'comment'		=> 'b.comment',
		'modifiedBy'	=> 'b.modified_by',
		'modifiedAt'	=> 'b.modified_at',
		'active'		=> 'b.active',
		'reasonId'		=> 'b.reason_id',
		
		'adminName'		=> 'a.name',

		'userName'		=> 'u.name',
		'userSurname'	=> 'u.surname',
	);
	protected $columnTypes = array(
		'id'			=> self::INT,
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
		
		'adminName'		=> self::TEXT,

		'userName'		=> self::TEXT,
		'userSurname'	=> self::TEXT,		
	);
	protected $tables = array(
		'b' => 'users_penalties',
	);
	protected $joins = array(
		'a' => 'admins',
		'u' => 'users',
	);
	protected $joinOns = array(
		'a' => 'b.admin_id=a.id',
		'u' => 'b.user_id=u.id',
	);
	protected $pk = 'b.id';
}
