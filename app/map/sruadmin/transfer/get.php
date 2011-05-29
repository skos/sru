<?php
/**
 * wyciagniecie statsów transferu
 */
class UFmap_SruAdmin_Transfer_Get
extends UFmap {

	protected $columns = array(
		'ip'		=> 'l.ip',
		'hostId'	=> 'c.id',
		'host'		=> 'c.host',
		'isAdmin'	=> 'c.can_admin',
		'exAdmin'	=> 'c.exadmin',
		'typeId'	=> 'c.type_id',
		'isBanned'	=> 'c.banned',
		'bytes_sum' 	=> 'sum(l.bytes)/1024/900',
		'bytes_min' 	=> 'min(l.bytes)/1024/60',
		'bytes_max' 	=> 'max(l.bytes)/1024/60',
	);
	protected $columnTypes = array(
		'ip'		=> self::TEXT,
		'hostId'	=> self::INT,
		'host'		=> self::TEXT,
		'bytes_sum'	=> self::INT,
		'bytes_min'	=> self::INT,
		'bytes_max'	=> self::INT,
		'isAdmin'	=> self::BOOL,
		'exAdmin'	=> self::BOOL,
		'typeId'	=> self::INT,
		'isBanned'	=> self::BOOL,
	);
	protected $tables = array(
		'l' => 'lanstats',
	);
	protected $joins = array( 
		'c' => 'computers',
	);
	protected $joinOns = array(
		'c' => 'l.ip=c.ipv4',
	);
	protected $pk = 'l.id';
}