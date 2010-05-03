<?php
/**
 * wyciagniecie pojedynczego portu switcha
 */
class UFmap_SruAdmin_SwitchPort_Get
extends UFmap {

	protected $columns = array(
		'id'			=> 'p.id',
		'ordinalNo'		=> 'p.ordinal_no',
		'switchId'		=> 'p.switch',
		'switchIp'		=> 's.ipv4',
		'locationId'		=> 'p.location',
		'locationAlias'		=> 'l.alias',
		'dormitoryAlias'	=> 'a.alias',
		'comment'		=> 'p.comment',
		'connectedSwitchId'	=> 'p.connected_switch',
		'connectedSwitchDorm'	=> 'd.alias',
		'connectedSwitchNo'	=> 'c.hierarchy_no',
		'connectedSwitchIp'	=> 'c.ipv4',
		'admin'			=> 'p.is_admin',
	);
	protected $columnTypes = array(
		'id'			=> self::INT,
		'ordinalNo'		=> self::INT,
		'switchId'		=> self::INT,
		'switchIp'		=> self::NULL_TEXT,
		'locationId'		=> self::NULL_INT,
		'locationAlias'		=> self::TEXT,
		'dormitoryAlias'	=> self::TEXT,
		'comment'		=> self::TEXT,
		'connectedSwitchId'	=> self::NULL_INT,
		'connectedSwitchDorm'	=> self::TEXT,
		'connectedSwitchNo'	=> self::NULL_INT,
		'connectedSwitchIp'	=> self::NULL_TEXT,
		'admin'			=> self::BOOL,
	);
	protected $tables = array(
		'p' => 'switches_port',
	);
	protected $joins = array(
		'l' => 'locations',
		'c' => 'switches',
		'd' => 'dormitories',
		's' => 'switches',
		'a' => 'dormitories',
	);
	protected $joinOns = array(
		'l' => 'p.location=l.id',
		'c' => 'p.connected_switch=c.id',
		'd' => 'c.dormitory=d.id',
		's' => 'p.switch=s.id',
		'a' => 'l.dormitory_id=a.id',
	);
	protected $pk = 'id';
}
