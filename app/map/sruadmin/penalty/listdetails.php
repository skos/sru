<?php
/**
 * wyciagniecie kar
 */
class UFmap_SruAdmin_Penalty_ListDetails
extends UFmap_SruAdmin_Penalty_Get {

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
		'templateId'   => 'b.template_id',
		'templateTitle' => 't.title',
		
		'userName'     => 'u.name',
		'userSurname'  => 'u.surname',
		'userLogin'    => 'u.login',
		'userDormAlias'=> 'd.alias',

		'creatorName'  => 'a.name',

		'modifierName' => 'c.name',

		'modificationCount' => '(SELECT count(*) FROM penalties_history h WHERE h.penalty_id = b.id)',
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
		'templateId'   => self::INT,
		'templateTitle' => self::TEXT,
		
		'userName'     => self::TEXT,
		'userSurname'  => self::TEXT,
		'userLogin'    => self::TEXT,
		'userDormAlias'=> self::TEXT,

		'creatorName'  => self::TEXT,

		'modifierName' => self::TEXT,

		'modificationCount' => self::INT,
	);
	protected $tables = array(
		'b' => 'penalties',
	);
	protected $joins = array( 
		'u' => 'users',
		'a' => 'admins',
		'c' => 'admins',
		't' => 'penalty_templates',
		'l' => 'locations',
		'd' => 'dormitories',
	);
	protected $joinOns = array(
		'u' => 'b.user_id=u.id',
		'a' => 'b.created_by=a.id',
		'c' => 'b.modified_by=c.id',
		't' => 'b.template_id=t.id',
		'l' => 'u.location_id=l.id',
		'd' => 'l.dormitory_id=d.id',
	);
	protected $pk = 'b.id';
}

