<?php
/**
 * wyciagniecie wyjatkow w fw
 */
class UFmap_SruAdmin_FwException_List
extends UFmap {

	protected $columns = array(
		'id'		=> 'e.id',
		'computerId'	=> 'e.computer_id',
		'host'		=> 'c.host',
		'ip'		=> 'c.ipv4',
		'port'		=> 'e.port',
		'active'	=> 'e.active',
		'waiting'	=> 'e.waiting',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'computerId'	=> self::INT,
		'host'		=> self::TEXT,
		'ip'		=> self::TEXT,
		'port'		=> self::INT,
		'active'	=> self::BOOL,
		'waiting'	=> self::BOOL,
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

