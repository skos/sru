<?
/**
 * zapisywanie panstwa
 */
class UFmap_SruWalet_Country_Set
extends UFmap {

	protected $columns = array(
		'id'			=> 'id',
		'name'			=> 'name',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'name'          => self::TEXT,
	);
	protected $tables = array(
		'' => 'countries',
	);
	protected $pk = 'id';
}
