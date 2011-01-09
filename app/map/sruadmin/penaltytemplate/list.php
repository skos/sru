<?php
/**
 * wyciagniecie kar
 */
class UFmap_SruAdmin_PenaltyTemplate_List
extends UFmap {

	protected $columns = array(
		'id'           => 't.id',
		'title'        => 't.title',
		'description'  => 't.description',
		'reason'       => 't.reason',
		'reasonEn'     => 't.reason_en',
		'typeId'       => 't.penalty_type_id',
		'duration'     => 't.duration',
		'amnesty'      => 't.amnesty_after',
		'active'       => 't.active',
	);
	protected $columnTypes = array(
		'id'           => self::INT,
		'title'        => self::TEXT,
		'description'  => self::TEXT,
		'reason'       => self::TEXT,
		'reasonEn'     => self::TEXT,
		'typeId'       => self::INT,
		'duration'     => self::INT,
		'amnesty'      => self::INT,
		'active'       => self::BOOL,
	);
	protected $tables = array(
		't' => 'penalty_templates',
	);
	protected $joins = array( 
	);
	protected $joinOns = array(
	);
	protected $pk = 't.id';
}

