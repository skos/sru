<?
/**
 * zapisywanie panstwa
 */
class UFmap_SruWalet_Country_Set
extends UFmap {

	protected $columns = array(
		'id'			=> 'id',
		'nationality'			=> 'nationality',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'nationality'          => self::TEXT,
	);
	protected $tables = array(
		'' => 'countries',
	);
	protected $pk = 'id';
}
