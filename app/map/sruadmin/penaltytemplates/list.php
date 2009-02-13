<?php
/**
 * wyciagniecie kar
 */
class UFmap_SruAdmin_PenaltyTemplates_List
extends UFmap {

	protected $columns = array(
		'id'           => 't.id',
		'title'        => 't.title',
		'description'  => 't.description',
		'type_id'      => 't.penalty_type_id',
		'duration'     => 't.duration',
	);
	protected $columnTypes = array(
		'id'           => self::INT,
		'title'        => self::TEXT,
		'description'  => self::TEXT,
		'type_id'      => self::INT,
		'duration'     => self::INT,
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

