<?
/**
 * wyciagniecie pojedynczego vlanu
 */
class UFmap_SruAdmin_Vlan_Get
extends UFmap {

	protected $columns = array(
		'id'			=> 'v.id',
		'name'			=> 'v.name',
		'description'	=> 'v.description',
		'domainSuffix'	=> 'v.domain_suffix',
	);
	protected $columnTypes = array(
		'id'			=> self::INT,
		'name'			=> self::TEXT,
		'description'	=> self::TEXT,
		'domainSuffix'	=> self::TEXT,
	);
	protected $tables = array(
		'v' => 'vlans',
	);
	protected $joins = array(
	);
	protected $joinOns = array(
	);
	protected $pk = 'v.id';
}
