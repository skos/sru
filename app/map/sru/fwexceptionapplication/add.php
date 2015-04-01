<?php
/**
 * dodanie wniosku
 */
class UFmap_Sru_FwExceptionApplication_Add
extends UFmap {
	protected $columns = array(
		'id'			=> 'id',
		'userId'		=> 'user_id',
		'validTo'		=> 'valid_to',
		'selfEducation'		=> 'self_education',
		'universityEducation'	=> 'university_education',
		'comment'		=> 'comment',
	);
	protected $columnTypes = array(
		'id'			=> self::INT,
		'userId'		=> self::INT,
		'validTo'		=> self::TS,
		'selfEducation'		=> self::BOOL,
		'universityEducation'	=> self::BOOL,
		'comment'		=> self::TEXT,
		'purpose'		=> self::INT,	// kolumna tylko do walidacji
	    	'newExceptions'		=> self::TEXT,	// kolumna tylko do walidacji
	);
	protected $tables = array(
		'' => 'fw_exception_applications',
	);

	protected $valids = array(
		'comment' => array('textMin'=>1),
		'newExceptions' => array('textMin'=>1),
		'purpose' => array('intMin'=>1),
	);
	
	protected $pk = 'id';
}

