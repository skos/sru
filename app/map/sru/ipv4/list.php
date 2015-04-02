<?
/**
 * wyciagniecie listy IP
 */
class UFmap_Sru_Ipv4_List
extends UFmap_Sru_Ipv4_Get {
	protected $columns = array(
		'ip'		=> 'i.ip',
		'dormitoryId'	=> 'i.dormitory_id',
		'vlanId'	=> 'i.vlan',
		'vlanName'	=> 'v.name',
		'domainSuffix'	=> 'v.domain_suffix',
	);
	protected $columnTypes = array(
		'ip'		=> self::TEXT,
		'dormitoryId'	=> self::INT,
		'host'		=> self::NULL_TEXT,
		'vlanId'	=> self::INT,
		'vlanName'	=> self::TEXT,
		'domainSuffix'	=> self::TEXT,
	);
	protected $tables = array(
		'i' => 'ipv4s',
	);
	protected $joins = array(
		'v' => 'vlans',
	);
	protected $joinOns = array(
		'i.vlan=v.id',
	);
	protected $pk = 'i.ip';
}