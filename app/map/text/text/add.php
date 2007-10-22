<?
/**
 * dodanie tekstu
 */
class UFmap_Text_Text_Add
extends UFmap {

	protected $columns = array(
		'alias'   => 'alias',
		'title'   => 'title',
		'content' => 'content',
	);
	protected $columnTypes = array(
		'alias'   => self::TEXT,
		'title'   => self::TEXT,
		'content' => self::TEXT,
	);
	protected $valids = array(
		'alias'   => array('textMin'=>1, 'textMax'=>30, 'regexp'=>'^[a-z0-9][-\.\/a-z0-9]*$'),
		'title'   => array('textMin'=>1, 'textMax'=>100),
	);
	protected $tables = array(
		'' => 'text',
	);
	protected $pk = 'id';
}
