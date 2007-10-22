<?
/**
 * wyciagniecie pojedynczego tekstu
 */
class UFmap_Text_Text_Get
extends UFmap {

	protected $columns = array(
		'id'      => 't.id',
		'alias'   => 't.alias',
		'title'   => 't.title',
		'content' => 't.content',
		'modifiedAt' => 't.modified_at',
	);
	protected $columnTypes = array(
		'id'      => self::INT,
		'alias'   => self::TEXT,
		'title'   => self::TEXT,
		'content' => self::TEXT,
		'modifiedAt' => self::TS,
	);
	protected $tables = array(
		't' => 'text',
	);
	protected $pk = 't.id';
}
