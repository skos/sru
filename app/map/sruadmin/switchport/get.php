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
		'switchNo'		=> 's.hierarchy_no',
		'switchSn'		=> 's.serial_no',
		'switchLab'		=> 's.lab',
		'locationId'		=> 'p.location',
		'locationAlias'		=> 'l.alias',
		'dormitoryAlias'	=> 'a.alias',
		'comment'		=> 'p.comment',
		'connectedSwitchId'	=> 'p.connected_switch',
		'connectedSwitchDorm'	=> 'd.alias',
		'connectedSwitchNo'	=> 'c.hierarchy_no',
		'connectedSwitchIp'	=> 'c.ipv4',
		'connectedSwitchSn'	=> 'c.serial_no',
		'connectedSwitchLab'=> 'c.lab',
		'admin'			=> 'p.is_admin',
		'penaltyId'			=> 'p.penalty_id',
		'penaltyReason'		=> 'e.reason',
		'userName'			=> 'u.name',
		'userSurname'		=> 'u.surname',
		'userLogin'			=> 'u.login',
		'templateTitle'		=> 't.title',
	);
	protected $columnTypes = array(
		'id'				=> self::INT,
		'ordinalNo'			=> self::INT,
		'switchId'			=> self::INT,
		'switchIp'			=> self::NULL_TEXT,
		'switchNo'			=> self::INT,
		'switchSn'			=> self::TEXT,
		'switchLab'			=> self::BOOL,
		'locationId'		=> self::NULL_INT,
		'locationAlias'		=> self::TEXT,
		'dormitoryAlias'	=> self::TEXT,
		'comment'			=> self::TEXT,
		'connectedSwitchId'	=> self::NULL_INT,
		'connectedSwitchDorm'	=> self::TEXT,
		'connectedSwitchNo'	=> self::NULL_INT,
		'connectedSwitchIp'	=> self::NULL_TEXT,
		'connectedSwitchSn'	=> self::TEXT,
		'connectedSwitchLab'=> self::BOOL,
		'admin'				=> self::BOOL,
		'penaltyId'			=> self::NULL_INT,
		'penaltyReason'     => self::TEXT,
		'userName'			=> self::TEXT,
		'userSurname'		=> self::TEXT,
		'userLogin'			=> self::TEXT,
		'templateTitle'		=> self::TEXT,
	);
	protected $tables = array(
		'p' => 'switches_port',
	);
	protected $joins = array(
		'l' => 'locations',
		'c' => 'switches',
		'o' => 'locations',
		'd' => 'dormitories',
		's' => 'switches',
		'a' => 'dormitories',
		'e' => 'penalties',
		'u' => 'users',
		't' => 'penalty_templates',
	);
	protected $joinOns = array(
		'l' => 'p.location=l.id',
		'c' => 'p.connected_switch=c.id',
		'o' => 'c.location_id=o.id',
		'd' => 'o.dormitory_id=d.id',
		's' => 'p.switch=s.id',
		'a' => 'l.dormitory_id=a.id',
		'e' => 'p.penalty_id=e.id',
		'u' => 'e.user_id=u.id',
		't' => 'e.template_id=t.id',
	);
	protected $pk = 'id';
}
