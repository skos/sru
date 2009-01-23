<?
/**
 * wybranie listy komputerow do listy ethers
 */
class UFmap_Sru_Computer_Ethers
extends UFmap {

	protected $columns = array(
		'ip'             => 'i.ip',
		'mac'            => "coalesce(c.mac,'00:00:00:00:00:00')",
		'active'         => 'c.active',
		'banned'         => 'c.banned',
	);
	protected $columnTypes = array(
		'ip'             => self::TEXT,
		'mac'            => self::TEXT,
		'host'           => self::TEXT,
		'active'         => self::NULL_BOOL,
		'banned'         => self::NULL_BOOL,
	);
	protected $tables = array(
		'i' => 'ipv4s',
	);
	protected $joins = array(
		'c' => 'computers',
	);
	protected $joinOns = array(
		'c' => '(i.ip=c.ipv4 AND c.active AND NOT c.banned)',
	);
	protected $pk = 'i.idp';
}
