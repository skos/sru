<?php
/**
 * dodanie kary
 */
class UFmap_SruAdmin_Penalty_Add
extends UFmap {

	protected $columns = array(
		'userId'       => 'user_id',
		'typeId'       => 'type_id',
		'startAt'      => 'start_at',
		'endAt'        => 'end_at',
		'reason'       => 'reason',
		'comment'      => 'comment',
		'createdById'  => 'created_by',
		'createdAt'    => 'created_at',
		'modifiedById' => 'modified_by',
		'modifiedAt'   => 'modified_at',
		'amnestyAt'    => 'amnesty_at',
		'amnestyAfter' => 'amnesty_after',
		'amnestyById'  => 'amnesty_by',
		'active'       => 'active',
		'templateId'   => 'template_id',
	);

	protected $columnTypes = array(
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
		'after'        => self::INT,	// tylko do walidacji formularza
		'duration'     => self::INT,	// tylko do walidacji formularza
		'computerId'   => self::NULL_INT,	// tylko do walidacji formularza
	);

	protected $tables = array(
		'' => 'penalties',
	);

	protected $valids = array(
		'typeId' => array('intMin'=>1),
		'after' => array('intMin'=>0),
		'reason' => array('textMin'=>1),
		'duration' => array('intMin'=>1),
	);

	protected $pk = 'id';
}
