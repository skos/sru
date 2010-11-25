<?php
/**
 * wyciagniecie statsów transferu
 */
class UFmap_SruAdmin_Transfer_List
extends UFmap {

	protected $columns = array(
		'mac'		=> 'l.mac',
		'bytes_sum' 	=> 'sum(l.bytes)/1024/1800',
		'bytes_min' 	=> 'min(l.bytes)/1024/10',
		'bytes_max' 	=> 'max(l.bytes)/1024/10',
	);
	protected $columnTypes = array(
		'mac'		=> self::TEXT,
		'bytes_sum'	=> self::INT,
		'bytes_min'	=> self::INT,
		'bytes_max'	=> self::INT,
	);
	protected $tables = array(
		'l' => 'lanstats',
	);
	protected $pk = 'l.id';
}

