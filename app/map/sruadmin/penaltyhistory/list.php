<?
/**
 * wyciagniecie historii modyfikikacji kary
 */
class UFmap_SruAdmin_PenaltyHistory_List
extends UFmap {

	protected $columns = array(
		'id'           => 'p.id',
		'penaltyId'    => 'p.penalty_id',
		'endAt'        => 'p.end_at',
		'reason'       => 'p.reason',
		'comment'      => 'p.comment',
		'modifiedById' => 'p.modified_by',
		'modifiedAt'   => 'p.modified_at',
		'amnestyAfter' => 'p.amnesty_after',
		'modifiedBy'   => 'a.name',
	);
	protected $columnTypes = array(
		'id'           => self::INT,
		'penaltyId'    => self::INT,
		'endAt'        => self::TS,
		'reason'       => self::TEXT,
		'comment'      => self::TEXT,
		'modifiedById' => self::NULL_INT, 
		'modifiedAt'   => self::NULL_TS,
		'amnestyAfter' => self::NULL_TS,
		'modifiedBy'   => self::TEXT,
	);
	protected $tables = array(
		'p' => 'penalties_history',
	);
	protected $joins = array(
		'a' => 'admins',
	);
	protected $joinOns = array(
		'a' => 'p.modified_by=a.id',
	);
	protected $pk = 'p.id';
}
