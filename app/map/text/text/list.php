<?
/**
 * wyciagniecie listy tekstow
 */
class UFmap_Text_Text_List
extends UFmap {

	protected $columns = array(
		'id'      => 't.id',
		'alias'   => 't.alias',
		'title'   => 't.title',
		'modifiedAt' => 't.modified_at',
	);
	protected $columnTypes = array(
		'id'      => self::INT,
		'alias'   => self::TEXT,
		'title'   => self::TEXT,
		'modifiedAt' => self::TS,
	);
	protected $tables = array(
		't' => 'text',
	);
	protected $pk = 't.id';
}
