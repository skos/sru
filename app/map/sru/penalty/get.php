<?php
/**
 * wyciagniecie pojedynczej kary
 */
class UFmap_Sru_Penalty_Get
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
	);
	protected $tables = array(
		'b' => 'penalties',
	);
	protected $pk = 'b.id';
}
