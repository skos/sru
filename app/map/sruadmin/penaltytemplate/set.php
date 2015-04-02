<?php
/**
 * zapis szablonu kary
 */
class UFmap_SruAdmin_PenaltyTemplate_Set
extends UFmap {

	protected $columns = array(
		'id'           => 'id',
		'title'        => 'title',
		'description'  => 'description',
		'reason'       => 'reason',
		'reasonEn'     => 'reason_en',
		'typeId'       => 'penalty_type_id',
		'duration'     => 'duration',
		'amnesty'      => 'amnesty_after',
		'active'       => 'active',
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
		'' => 'penalty_templates',
	);
	protected $valids = array(
		'title' => array('textMin'=>1, 'textMax'=>100),
		'description' => array('textMin'=>1), 
	);
	protected $pk = 'id';
}

