<?php
/**
 * wyciagniecie wyjatkow w fw
 */
class UFmap_SruAdmin_FwExceptions_List
extends UFmap {

	protected $columns = array(
		'computerId'	=> 'e.computer_id',
		'host'		=> 'c.host',
		'ip'		=> 'c.ipv4',
		'port'		=> 'e.port',
		'comment'	=> 'e.comment',
		'active'	=> 'e.active',
	);
	protected $columnTypes = array(
		'computerId'	=> self::INT,
		'host'		=> self::TEXT,
		'ip'		=> self::TEXT,
		'port'		=> self::INT,
		'comment'	=> self::NULL_TEXT,
		'active'	=> self::BOOL,
	);
	protected $tables = array(
		'e' => 'fw_exceptions',
	);
	protected $joins = array(
		'c' => 'computers',
	);
	protected $joinOns = array(
		'c' => 'e.computer_id=c.id',
	);
	protected $pk = 'i.id';
}

