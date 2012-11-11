<?php
/**
 * wyciagniecie kar
 */
class UFmap_SruAdmin_Ips_List
extends UFmap {

	protected $columns = array(
		'ip'           => 'i.ip',
		'dormitoryId'  => 'i.dormitory_id',
		'vlan'		   => 'i.vlan',
		'dormitoryAlias' => 'id.alias',
		'computerId'   => 'c.id',
		'computerHost' => 'c.host',
		'admin' => 'c.can_admin',
		'exAdmin' => 'c.exadmin',
		'banned' => 'c.banned',
		'computerDormitoryId' => 'cl.dormitory_id',
		'computerDormitoryAlias' => 'cd.alias',
	);
	protected $columnTypes = array(
		'ip'           => self::TEXT,
		'dormitoryId'  => self::NULL_INT,
		'vlan'		   => self::INT,
		'dormitoryAlias' => self::NULL_TEXT,
		'computerId'   => self::NULL_INT,
		'computerHost' => self::NULL_TEXT,
		'admin'		=> self::BOOL,
		'exAdmin'		=> self::BOOL,
		'banned'	=> self::BOOL,
		'computerDormitoryId' => self::NULL_INT,
		'computerDormitoryAlias' => self::NULL_TEXT,
	);
	protected $tables = array(
		'i' => 'ipv4s',
	);
	protected $joins = array(
		'id' => 'dormitories',
		'c' => 'computers',
		'cl' => 'locations',
		'cd' => 'dormitories',
	);
	protected $joinOns = array(
		'id' => 'i.dormitory_id = id.id',
		'c' => '(i.ip=c.ipv4 and c.active)',
		'cl' => 'c.location_id = cl.id',
		'cd' => 'cl.dormitory_id = cd.id',
	);
	protected $pk = 'i.id';
}

