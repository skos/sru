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
		'recordType'	=> 'record_type',
		'domainName'	=> 'domain_name',
		'value'			=> 'value',
		'availTo'		=> 'avail_to',
	);

	protected $columnTypes = array(
		'id'		=> self::INT,
		'computerId'	=> self::INT,
		'host'		=> self::TEXT,
		'recordType'	=> self::INT,
		'domainName'	=> self::TEXT,
		'value'			=> self::NULL_TEXT,
		'availTo'		=> self::NULL_TS
	);
	protected $tables = array(
		'' => 'computers_aliases',
	);

	protected $pk = 'id';

	protected $valids = array(
		'host' => array('textMin'=>1, 'textMax'=>50, 'regexp'=>'^[_]?[a-z][-a-z0-9]*[a-z0-9]$|^[_]?[a-z][-a-z0-9.]*[-a-z0-9]+[a-z0-9]$'),
		'value' => array('regexp'=>'^[-_a-zA-Z0-9]+$'),
	);
}
