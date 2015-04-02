<?
/**
 * panstwo
 */
class UFmap_SruWalet_Country_Get
extends UFmap {

	protected $columns = array(
		'id'			=> 'c.id',
		'nationality'		=> 'c.nationality',
		'nationalitySearch'	=> 'lower(c.nationality)',
	);
	protected $columnTypes = array(
		'id'                    => self::INT,
		'nationality'           => self::TEXT,
		'nationalitySearch'     => self::TEXT,
	);
	protected $tables = array(
		'c' => 'countries',
	);
	protected $joins = array(
	);
	protected $joinOns = array(
	);
	protected $pk = 'c.id';
}
