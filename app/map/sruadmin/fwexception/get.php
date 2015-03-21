<?php
/**
 * pobraniewyjatku w fw
 */
class UFmap_SruAdmin_FwException_Get
extends UFmap {

	protected $columns = array(
		'id'		=> 'id',
		'computerId'	=> 'computer_id',
		'port'		=> 'port',
		'active'	=> 'active',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'computerId'	=> self::INT,
		'port'		=> self::INT,
		'active'	=> self::BOOL,
	);
	protected $tables = array(
		'' => 'fw_exceptions',
	);
	protected $pk = 'id';
}

