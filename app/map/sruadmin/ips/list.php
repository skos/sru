<?php
/**
 * wyciagniecie adresów IP
 */
class UFmap_SruAdmin_Ips_List
extends UFmap {

	protected $columns = array(
		'ip'           => 'i.ip',
		'dormitoryId'  => 'i.dormitory_id',
		'vlan'		   => 'i.vlan',
		'vlanName'	=> 'v.name',
		'vlanDesc'	=> 'v.description',
		'vlanDomainSuffix'	=> 'v.domain_suffix',
		'dormitoryAlias' => 'id.alias',
		'computerId'   => 'c.id',
		'computerHost' => 'c.host',
		'switchId'   => 's.id',
		'switchModel'   => 'sm.model_name',
		'switchSerialNo'   => 'si.serial_no',
		'inoperational' => 's.inoperational',
		'admin' => 'c.can_admin',
		'exAdmin' => 'c.exadmin',
		'banned' => 'c.banned',
		'computerDormitoryId' => 'cl.dormitory_id',
		'computerDormitoryAlias' => 'cd.alias',
		'switchDormitoryAlias' => 'sd.alias',
	);
	protected $columnTypes = array(
		'ip'           => self::TEXT,
		'dormitoryId'  => self::NULL_INT,
		'vlan'		   => self::INT,
		'vlanName' => self::NULL_TEXT,
		'vlanDesc' => self::NULL_TEXT,
		'vlanDomainSuffix' => self::TEXT,
		'dormitoryAlias' => self::NULL_TEXT,
		'computerId'   => self::NULL_INT,
		'computerHost' => self::NULL_TEXT,
	    	'switchId'   => self::NULL_INT,
		'switchModel'   => self::NULL_TEXT,
		'switchSerialNo'   => self::NULL_TEXT,
		'inoperational' => self::BOOL,
		'admin'		=> self::BOOL,
		'exAdmin'		=> self::BOOL,
		'banned'	=> self::BOOL,
		'computerDormitoryId' => self::NULL_INT,
		'computerDormitoryAlias' => self::NULL_TEXT,
		'switchDormitoryAlias' => self::NULL_TEXT,
	);
	protected $tables = array(
		'i' => 'ipv4s',
	);
	protected $joins = array(
		'id' => 'dormitories',
		'c' => 'computers',
		'cl' => 'locations',
		'cd' => 'dormitories',
		's' => 'switches',
		'sm' => 'switches_model',
		'sl' => 'locations',
		'sd' => 'dormitories',
		'si' => 'inventory_cards',
		'v' => 'vlans',
	);
	protected $joinOns = array(
		'id' => 'i.dormitory_id = id.id',
		'c' => '(i.ip=c.ipv4 and c.active)',
		'cl' => 'c.location_id = cl.id',
		'cd' => 'cl.dormitory_id = cd.id',
		's' => 'i.ip=s.ipv4',
		'sm' => 's.model = sm.id',
		'sl' => 's.location_id = sl.id',
		'sd' => 'sl.dormitory_id = sd.id',
		'si' => 's.inventory_card_id = si.id',
		'v' => 'i.vlan = v.id',
	);
	protected $pk = 'i.id';
}

