<?
/**
 * zmodyfikowanie tekstu
 */
class UFmap_Text_Text_Set
extends UFmap {

	protected $columns = array(
		'alias'   => 'alias',
		'title'   => 'title',
		'content' => 'content',
		'modifiedAt' => 'modified_at',
	);
	protected $columnTypes = array(
		'id'      => self::INT,
		'alias'   => self::TEXT,
		'title'   => self::TEXT,
		'content' => self::TEXT,
		'modifiedAt' => self::TS,
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
