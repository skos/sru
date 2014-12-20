<?php
/**
 * wyciagniecie switchy na potrzeby DNS
 */
class UFmap_SruAdmin_Switch_Dns
extends UFmap {

	protected $columns = array(
		'id'		=> 's.id',
		'hierarchyNo'	=> 's.hierarchy_no',
		'ip'		=> 's.ipv4',
		'lab'		=> 's.lab',
		'dormitoryAlias'=> 'd.alias',
		'domainSuffix'	=> 'v.domain_suffix',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'hierarchyNo'	=> self::NULL_INT,
		'ip'		=> self::NULL_TEXT,
		'lab'		=> self::BOOL,
		'dormitoryAlias'=> self::TEXT,
		'domainSuffix'	=> self::TEXT,
	);
	protected $tables = array(
		's' => 'switches',
	);
	protected $joins = array(
		'l' => 'locations',
		'd' => 'dormitories',
		'i' => 'ipv4s',
		'v' => 'vlans',
	);	
	protected $joinOns = array(
		'l' => 's.location_id=l.id',
		'd' => 'l.dormitory_id=d.id',
		'i' => 's.ipv4 = i.ip',
		'v' => 'i.vlan = v.id',
	);
	protected $pk = 'id';
}
