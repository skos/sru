<?
/**
 * wyciagniecie zliczenia ip
 */
class UFmap_Sru_Ipv4_Count
extends UFmap {

	protected $columns = array(
		'ip'		=> 'count(i.ip)',
	);
	protected $columnTypes = array(
		'ip'		=> self::TEXT,
	);
	protected $tables = array(
		'i' => 'ipv4s',
	);
	protected $joins = array(
		'c' => 'computers',
	);
	protected $joinOns = array(
		'c' => '(i.ip=c.ipv4 and c.active)',
	);
	protected $pk = 'i.id';
}
