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
		'startAt'		=> 'b.start_at',
		'endAt'			=> 'b.end_at',
		'comment'		=> 'b.comment',
		'modifiedBy'	=> 'b.modified_by',
		'modifiedAt'	=> 'b.modified_at',
		'amnestyAt'		=> 'b.amnesty_at',
		'amnestyAfter'	=> 'b.amnesty_after',
		'amnestyBy'		=> 'b.amnesty_by',
		'createdAt'		=> 'b.created_at',
		'reason'		=> 'b.reason',
		
		'adminName'		=> 'a1.name',
		
		'modifyAdminName'=> 'a2.name',	
		'amnestyAdminName'=> 'a3.name',	

		'userName'		=> 'u.name',
		'userSurname'	=> 'u.surname',
		'userLogin'		=> 'u.login',
	);
	protected $columnTypes = array(
		'id'			=> self::INT,
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
		
		'adminName'		=> self::TEXT,
		'modifyAdminName'=> self::TEXT,
		'amnestyAdminName'=> self::TEXT,

		'userName'		=> self::TEXT,
		'userSurname'	=> self::TEXT,	
		'userLogin'	=> self::TEXT,	
	);
	protected $tables = array(
		'b' => 'penalties',
	);
	protected $joins = array( 
		'a1' => 'admins', //pewnie da se prosciej?
		'a2' => 'admins',
		'a3' => 'admins',
		'u' => 'users',
	);
	protected $joinOns = array(
		'a1' => 'b.admin_id=a1.id',
		'a2' => 'b.modified_by=a2.id',
		'a3' => 'b.amnesty_by=a3.id',
		'u' => 'b.user_id=u.id',
	);
	protected $pk = 'b.id';
}
