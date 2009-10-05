<?php
/**
 * wyciagniecie listy migracji
 */
class UFmap_SruAdmin_Migration_List
extends UFmap {

	protected $columns = array(
		'hash'	=> 'w.hash',
		'room'	=> 'w.room',
		'dorm'	=> 'w.dorm',
	);
	protected $columnTypes = array(
		'hash'	=> self::TEXT,
		'room'	=> self::TEXT,
		'dorm'	=> self::TEXT,
	);
	protected $tables = array(
		'w' => 'users_walet',
	);
	protected $pk = 'w.id';
}

