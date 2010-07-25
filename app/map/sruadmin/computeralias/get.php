<?
/**
 * dodanie aliasu komputera
 */
class UFmap_SruAdmin_ComputerAlias_Get
extends UFmap {
	protected $columns = array(
		'id'             => 'id',
		'computerId'     => 'computer_id',
		'host'           => 'host',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'computerId'     => self::INT,
		'host'           => self::TEXT,
	);
	protected $tables = array(
		'' => 'computers_aliases',
	);
	protected $pk = 'id';
}
