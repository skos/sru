<?
/**
 * lista kar
 */
class UFmap_SruAdmin_ComputerBan_List
extends UFmap {

	protected $columns = array(
		'computerId'   => 'b.computer_id',
		'penaltyId'    => 'b.penalty_id',
		'active'       => 'b.active',
		'computerHost' => 'c.host',
		'computerIp'   => 'c.ipv4',
		'id'           => 'p.id',
		'userId'       => 'p.user_id',
		'typeId'       => 'p.type_id',
		'startAt'      => 'p.start_at',
		'endAt'        => 'p.end_at',
		'createdById'  => 'p.created_by',
		'createdAt'    => 'p.created_at',
		'modifiedById' => 'p.modified_by',
		'modifiedAt'   => 'p.modified_at',
		'active'       => 'b.active',
		'templateId'   => 'p.template_id',
		'templateTitle' => 't.title',
		'reason'	=> 'p.reason',
	);

	protected $columnTypes = array(
		'computerId'   => self::INT,
		'penaltyId'    => self::INT,
		'active'       => self::BOOL,
		'computerHost' => self::TEXT,
		'computerIp'   => self::TEXT,
		'id'           => self::INT,
		'userId'       => self::INT,
		'typeId'       => self::INT,
		'startAt'      => self::TS,
		'endAt'        => self::TS,
		'createdById'  => self::INT,
		'createdAt'    => self::TS,
		'modifiedById' => self::NULL_INT, 
		'modifiedAt'   => self::NULL_TS,
		'active'       => self::BOOL,
		'templateId'   => self::INT,
		'templateTitle' => self::TEXT,
		'reason'	=> self::TEXT,
	);

	protected $tables = array(
		'b' => 'computers_bans',
	);

	protected $joins = array(
		'c' => 'computers',
		'p' => 'penalties',
		't' => 'penalty_templates',
	);

	protected $joinOns = array(
		'c' => 'b.computer_id=c.id',
		'p' => 'b.penalty_id=p.id',
		't' => 'p.template_id=t.id'
	);

	protected $pk = 'id';
}
