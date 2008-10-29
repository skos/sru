<?php
/**
 * wyciagniecie pojedynczej kary
 */
class UFmap_SruAdmin_Penalty_Get
extends UFmap {

	protected $columns = array(
		'id'           => 'b.id',
		'userId'       => 'b.user_id',
		'typeId'       => 'b.type_id',
		'startAt'      => 'b.start_at',
		'endAt'        => 'b.end_at',
		'reason'       => 'b.reason',
		'comment'      => 'b.comment',
		'createdById'  => 'b.created_by',
		'createdAt'    => 'b.created_at',
		'modifiedById' => 'b.modified_by',
		'modifiedAt'   => 'b.modified_at',
		'amnestyAt'    => 'b.amnesty_at',
		'amnestyAfter' => 'b.amnesty_after',
		'amnestyById'  => 'b.amnesty_by',
		'active'       => 'b.active',
		
		'createdByName' => 'a1.name',
		
		'modifiedByName' => 'a2.name',
		'amnestyByName' => 'a3.name',

		'userName'     => 'u.name',
		'userSurname'  => 'u.surname',
		'userLogin'    => 'u.login',
	);
	protected $columnTypes = array(
		'id'           => self::INT,
		'userId'       => self::INT,
		'typeId'       => self::INT,
		'startAt'      => self::TS,
		'endAt'        => self::TS,
		'reason'       => self::TEXT,
		'comment'      => self::TEXT,
		'createdById'  => self::INT,
		'createdAt'    => self::TS,
		'modifiedById' => self::NULL_INT, 
		'modifiedAt'   => self::NULL_TS,
		'amnestyAt'    => self::NULL_TS,
		'amnestyAfter' => self::NULL_TS,
		'amnestyById'  => self::NULL_INT,
		'active'       => self::BOOL,
		
		'createdByName' => self::TEXT,
		'modifiedByName' => self::TEXT,
		'amnestyByName' => self::TEXT,

		'userName'     => self::TEXT,
		'userSurname'  => self::TEXT,
		'userLogin'    => self::TEXT,
	);
	protected $tables = array(
		'b' => 'penalties',
	);
	protected $joins = array( 
		'a1' => 'admins',
		'a2' => 'admins',
		'a3' => 'admins',
		'u' => 'users',
	);
	protected $joinOns = array(
		'a1' => 'b.created_by=a1.id',
		'a2' => 'b.modified_by=a2.id',
		'a3' => 'b.amnesty_by=a3.id',
		'u' => 'b.user_id=u.id',
	);
	protected $pk = 'b.id';
}
