<?
/**
 * wybranie listy komputerow do listy DNS
 */
class UFmap_Sru_Computer_Dns
extends UFmap {

	protected $columns = array(
		'ip'             => 'i.ip',
		'mac'            => 'c.mac',
		'host'           => 'c.host',
		'active'         => 'c.active',
		'domainSuffix'	 => 'v.domain_suffix',
	);
	protected $columnTypes = array(
		'ip'             => self::TEXT,
		'mac'            => self::TEXT,
		'host'           => self::TEXT,
		'active'         => self::NULL_BOOL,
		'domainSuffix'	 => self::TEXT,
	);
	protected $tables = array(
		'c' => 'computers',
	);
	protected $joins = array(
		'i' => 'ipv4s',
		'v' => 'vlans',
	);
	protected $joinOns = array(
		'i' => 'c.ipv4 = i.ip',
		'v' => 'i.vlan = v.id',
	);
	protected $pk = 'c.id';
}
