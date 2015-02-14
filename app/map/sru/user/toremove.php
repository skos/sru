<?php
/**
 * wyciagniecie listy userow
 */
class UFmap_Sru_User_ToRemove
extends UFmap {

	protected $columns = array(
		'id'		=> 'u.id',
		'deactivated'	=> 'max(u.modified_at)',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'deactivated'	=> self::DATE,
	);
	protected $tables = array(
		'u' => 'users',
	);
	protected $pk = 'u.id';
}