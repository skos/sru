<?
/**
 * alias komputera
 */
class UFmap_SruAdmin_ComputerAlias_Get
extends UFmap {
	protected $columns = array(
		'id'		=> 'a.id',
		'computerId'	=> 'a.computer_id',
		'host'		=> 'a.host',
		'recordType'	=> 'a.record_type',
		'domainName'	=> 'a.domain_name',
		'ip'		=> 'c.ipv4',
		'parent'	=> 'c.host',
		'parentWithDomain'	=> 'c.domain_Name',
		'parentComment'	=> 'c.comment',
		'parentBanned'	=> 'c.banned',
		'domainSuffix'	=> 'v.domain_suffix',
		'value'			=> 'a.value',
		'availTo'		=> 'a.avail_to',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'computerId'	=> self::INT,
		'host'		=> self::TEXT,
		'recordType'	=> self::INT,
		'domainName'	=> self::TEXT,
		'ip'		=> self::TEXT,
		'parent'	=> self::TEXT,
		'parentWithDomain'	=> self::TEXT,
		'parentComment'	=> self::TEXT,
		'parentBanned'	=> self::BOOL,
		'domainSuffix'	=> self::TEXT,
		'value'			=> self::NULL_TEXT,
		'availTo'		=> self::NULL_TS
	);
	protected $tables = array(
		'a' => 'computers_aliases',
	);
	protected $joins = array(
		'c' => 'computers',
		'i' => 'ipv4s',
		'v' => 'vlans',
	);
	protected $joinOns = array(
		'c' => 'a.computer_id = c.id',
		'i' => 'c.ipv4 = i.ip',
		'v' => 'i.vlan = v.id',
	);
	protected $pk = 'id';
}
