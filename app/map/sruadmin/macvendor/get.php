<?
/**
 * wyciagniecie pojedynczego dostawcy MACa
 */
class UFmap_SruAdmin_MacVendor_Get
extends UFmap {

	protected $columns = array(
		'mac'		=> 'v.mac',
		'vendor'	=> 'v.vendor',
	);
	protected $columnTypes = array(
		'mac'		=> self::TEXT,
		'vendor'	=> self::TEXT,
	);
	protected $tables = array(
		'v' => 'mac_vendors',
	);
	protected $joins = array(
	);
	protected $joinOns = array(
	);
	protected $pk = 'v.id';
}

