<?
/**
 * wyciagniecie pojedynczego ip
 */
class UFmap_Sru_Ipv4_Get
extends UFmap {

	protected $columns = array(
		'ip'          => 'i.ip',
		'dormitoryId' => 'i.dormitory_id',
		'host'        => 'c.host',
	);
	protected $columnTypes = array(
		'ip'          => self::TEXT,
		'dormitoryId' => self::INT,
		'host'        => self::NULL_TEXT,
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
