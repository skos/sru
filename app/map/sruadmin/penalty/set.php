<?
/**
 * modyfikacja kary
 */
class UFmap_SruAdmin_Penalty_Set
extends UFmap {

	protected $columns = array(
		'endAt'        => 'end_at',
		'modifiedById' => 'modified_by',
		'modifiedAt'   => 'modified_at',
		'amnestyAt'    => 'amnesty_at',
		'amnestyById'  => 'amnesty_by',
		'active'       => 'active',
		'reason'       => 'reason',
		'comment'      => 'comment',
		'amnestyAfter' => 'amnesty_after',
	);

	protected $columnTypes = array(
		'endAt'        => self::TS,
		'modifiedById' => self::NULL_INT, 
		'modifiedAt'   => self::NULL_TS,
		'amnestyAt'    => self::NULL_TS,
		'amnestyById'  => self::NULL_INT,
		'active'       => self::BOOL,
		'reason'       => self::TEXT,
		'comment'      => self::TEXT,
		'amnestyAfter' => self::NULL_TS,
		'after'        => self::INT,	// tylko do walidacji formularza
	);

	protected $tables = array(
		'' => 'penalties',
	);

	protected $pk = 'id';
}
