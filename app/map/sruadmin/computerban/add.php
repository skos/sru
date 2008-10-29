<?
/**
 * dodanie kary
 */
class UFmap_SruAdmin_ComputerBan_Add
extends UFmap {

	protected $columns = array(
		'computerId'   => 'computer_id',
		'penaltyId'    => 'penalty_id',
	);

	protected $columnTypes = array(
		'computerId'   => self::INT,
		'penaltyId'    => self::INT,
	);

	protected $tables = array(
		'' => 'computers_bans',
	);

	protected $pk = 'id';
}
