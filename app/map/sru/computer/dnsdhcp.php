<?
/**
 * wybranie listy komputerow do listy DNS i DHCP
 */
class UFmap_Sru_Computer_DnsDhcp
extends UFmap {

	protected $columns = array(
		'ip'             => 'i.ip',
		'mac'            => 'c.mac',
		'host'           => 'c.host',
		'active'         => 'c.active',
		'banned'	 => 'c.banned',
		'domainSuffix'	 => 'v.domain_suffix',
	);
	protected $columnTypes = array(
		'ip'             => self::TEXT,
		'mac'            => self::TEXT,
		'host'           => self::TEXT,
		'active'         => self::NULL_BOOL,
		'banned'         => self::BOOL,
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
