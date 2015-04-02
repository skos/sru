<?
/**
 * wyciagniecie pojedynczego ip
 */
class UFmap_Sru_Ipv4_Get
extends UFmap {

	protected $columns = array(
		'ip'		=> 'i.ip',
		'dormitoryId'	=> 'i.dormitory_id',
		'vlan'		=> 'i.vlan',
		'host'		=> 'c.host',
		'taskExport'	=> 'v.task_export',
		'domainSuffix'	=> 'v.domain_suffix',
	);
	protected $columnTypes = array(
		'ip'		=> self::TEXT,
		'dormitoryId'	=> self::NULL_INT,
		'vlan'		=> self::INT,
		'host'		=> self::NULL_TEXT,
		'taskExport'	=> self::BOOL,
		'domainSuffix'	=> self::TEXT,
	);
	protected $tables = array(
		'i' => 'ipv4s',
	);
	protected $joins = array(
		'c' => 'computers',
		'h' => 'computers_history',
		'v' => 'vlans',
	);
	protected $joinOns = array(
		'c' => '(i.ip=c.ipv4 and c.active)',
		'v' => 'i.vlan = v.id',
	);
	protected $pk = 'i.ip';
}
