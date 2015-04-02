<?
/**
 * wyciagniecie krajow
 */
class UFmap_SruWalet_Country_List
extends UFmap {

	protected $columns = array(
		'id'				=> 'c.id',
		'nationality'		=> 'c.nationality',
		'nationalitySearch'	=> 'lower(c.nationality)',
		'nationalityUsers'	=> '(SELECT count(*) FROM users where nationality = c.id)',
	);
	protected $columnTypes = array(
		'id'				=> self::INT,
		'nationality'		=> self::TEXT,
		'nationalitySearch'	=> self::TEXT,
		'nationalityUsers'	=> self::INT,
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
