<?php
/**
 * wyciagniecie kar
 */
class UFmap_SruAdmin_Ips_List
extends UFmap {

	protected $columns = array(
		'ip'		=> 'i.ip',
		'dormName'	=> 'd.name',
		'dormAlias'	=> 'd.alias',
		'host'		=> 'c.host',
		'hostId'	=> 'c.id',
		'factDorm'	=> 'f.name',
		'factDormAlias'	=> 'f.alias',
		'userName'	=> 'u.name',
		'userSurname'	=> 'u.surname',
		'userLogin'	=> 'u.login',
	);
	protected $columnTypes = array(
		'ip'		=> self::TEXT,
		'dormName'	=> self::TEXT,
		'dormAlias'	=> self::TEXT,
		'host'		=> self::NULL_TEXT,
		'hostId'	=> self::INT,
		'factDorm'	=> self::TEXT,
		'factDormAlias'	=> self::TEXT,
		'userName'	=> self::TEXT,
		'userSurname'	=> self::TEXT,
		'userLogin'	=> self::TEXT,
	);
	protected $tables = array(
		'i' => 'ipv4s',
	);
	protected $joins = array(
		'c' => 'computers',
		'l' => 'locations',
		'f' => 'dormitories',
		'd' => 'dormitories',
		'u' => 'users',
	);
	protected $joinOns = array(
		'c' => '(i.ip=c.ipv4 and c.active)',
		'd' => 'i.dormitory_id = d.id',
		'l' => 'c.location_id = l.id',
		'f' => 'l.dormitory_id = f.id',
		'u' => 'c.user_id = u.id',
	);
	protected $pk = 'i.id';
}

