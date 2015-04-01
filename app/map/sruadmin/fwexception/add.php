<?php
/**
 * dodanie wyjatku w fw
 */
class UFmap_SruAdmin_FwException_Add
extends UFmap {

	protected $columns = array(
		'id'		=> 'id',
		'computerId'	=> 'computer_id',
		'port'		=> 'port',
		'active'	=> 'active',
		'waiting'	=> 'waiting',
		'applicationId'	=> 'fw_exception_application_id',
		'modifiedBy'	=> 'modified_by',
		'modifiedAt'	=> 'modified_at',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'computerId'	=> self::INT,
		'port'		=> self::INT,
		'active'	=> self::BOOL,
		'waiting'	=> self::BOOL,
		'applicationId'	=> self::NULL_INT,
		'modifiedBy'	=> self::NULL_INT,
		'modifiedAt'	=> self::TS,
	);
	protected $tables = array(
		'' => 'fw_exceptions',
	);
	protected $pk = 'id';
	
	protected $valids = array(
		'port' => array('textMin'=>1, 'textMax'=>5, 'regexp'=>'^[0-9]{1,5}$'),
	);
}

