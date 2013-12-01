<?
/**
 * wybranie listy komputerow do listy ethers
 */
class UFmap_Sru_Computer_SkosEthers
extends UFmap {

	protected $columns = array(
		'ip'             => 'i.ip',
		'mac'            => 'c.mac',
		'active'         => 'c.active',
		'banned'         => 'c.banned',
		'taskExport'	 => 'v.task_export',
		'vlanId'	 => 'i.vlan',
	);
	protected $columnTypes = array(
		'ip'             => self::TEXT,
		'mac'            => self::TEXT,
		'host'           => self::TEXT,
		'active'         => self::NULL_BOOL,
		'banned'         => self::NULL_BOOL,
		'taskExport'	 => self::BOOL,
		'vlanId'	 => self::INT,
	);
	protected $tables = array(
		'i' => 'ipv4s',
	);
	protected $joins = array(
		'c' => 'computers',
		'v' => 'vlans',
	);
	protected $joinOns = array(
		'c' => '(i.ip=c.ipv4 AND c.active AND NOT c.banned)',
		'v' => 'i.vlan = v.id',
	);
	protected $pk = 'i.idp';
}
