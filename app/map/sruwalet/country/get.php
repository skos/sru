<?
/**
 * panstwo
 */
class UFmap_SruWalet_Country_Get
extends UFmap {

	protected $columns = array(
		'id'			=> 'c.id',
		'name'			=> 'c.name',
		'nameSearch'	=> 'lower(c.name)',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'name'          => self::TEXT,
		'nameSearch'    => self::TEXT,
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
