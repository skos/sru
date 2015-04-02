<?
/**
 * dodanie aliasu komputera
 */
class UFmap_SruAdmin_ComputerAlias_Add
extends UFmap {
	protected $columns = array(
		'id'		=> 'id',
		'computerId'	=> 'computer_id',
		'host'		=> 'host',
		'isCname'	=> 'is_cname',
		'domainName'	=> 'domain_name',
	);

	protected $columnTypes = array(
		'id'		=> self::INT,
		'computerId'	=> self::INT,
		'host'		=> self::TEXT,
		'isCname'	=> self::BOOL,
		'domainName'	=> self::TEXT,
	);
	protected $tables = array(
		'' => 'computers_aliases',
	);

	protected $pk = 'id';

	protected $valids = array(
		'host' => array('textMin'=>1, 'textMax'=>50, 'regexp'=>'^[a-z][-a-z0-9]*[a-z0-9]$|^[a-z][-a-z0-9.]*[-a-z0-9]+[a-z0-9]$'),
	);
}
