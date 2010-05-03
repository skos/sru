<?php
/**
 * dodanie pojedynczego portu switcha
 */
class UFmap_SruAdmin_SwitchPort_Add
extends UFmap {

	protected $columns = array(
		'id'			=> 'id',
		'ordinalNo'		=> 'ordinal_no',
		'switchId'		=> 'switch',
		'locationId'		=> 'location',
		'comment'		=> 'comment',
		'connectedSwitchId'	=> 'connected_switch',
		'admin'			=> 'is_admin',
	);
	protected $columnTypes = array(
		'id'			=> self::INT,
		'ordinalNo'		=> self::INT,
		'switchId'		=> self::INT,
		'locationId'		=> self::NULL_INT,
		'comment'		=> self::TEXT,
		'connectedSwitchId'	=> self::NULL_INT,
		'admin'			=> self::BOOL,
	);
	protected $tables = array(
		'' => 'switches_port',
	);
	protected $pk = 'id';
}
