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
	);

	protected $columnTypes = array(
		'computerId'   => self::INT,
		'penaltyId'    => self::INT,
		'active'       => self::BOOL,
		'computerHost' => self::TEXT,
	);

	protected $tables = array(
		'b' => 'computers_bans',
	);

	protected $joins = array(
		'c' => 'computers',
	);

	protected $joinOns = array(
		'c' => 'b.computer_id=c.id',
	);

	protected $pk = 'id';
}
