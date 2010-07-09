<?
/**
 * wyciagniecie pojedynczego ip
 */
class UFmap_Sru_Ipv4_Get
extends UFmap {

	protected $columns = array(
		'ip'		=> 'i.ip',
		'dormitoryId'	=> 'i.dormitory_id',
		'host'		=> 'c.host',
		'lastVisible'	=> '(SELECT modified_at FROM computers_history h where h.ipv4=i.ip order by modified_at desc limit 1)',
	);
	protected $columnTypes = array(
		'ip'		=> self::TEXT,
		'dormitoryId'	=> self::INT,
		'host'		=> self::NULL_TEXT,
		'lastVisible'	=> self::TS,
	);
	protected $tables = array(
		'i' => 'ipv4s',
	);
	protected $joins = array(
		'c' => 'computers',
		'h' => 'computers_history',
	);
	protected $joinOns = array(
		'c' => '(i.ip=c.ipv4 and c.active)',
	);
	protected $pk = 'i.id';
}
