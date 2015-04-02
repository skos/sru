<?php
/**
 * aktualizacja portu switcha
 */
class UFmap_SruAdmin_SwitchPort_Set
extends UFmap {

	protected $columns = array(
		'id'				=> 'id',
		'ordinalNo'			=> 'ordinal_no',
		'switchId'			=> 'switch',
		'locationId'		=> 'location',
		'comment'			=> 'comment',
		'connectedSwitchId'	=> 'connected_switch',
		'admin'				=> 'is_admin',
		'penaltyId'			=> 'penalty_id',
	);
	protected $columnTypes = array(
		'id'				=> self::INT,
		'ordinalNo'			=> self::INT,
		'switchId'			=> self::INT,
		'locationId'		=> self::NULL_INT,
		'comment'			=> self::TEXT,
		'connectedSwitchId'	=> self::NULL_INT,
		'admin'				=> self::BOOL,
		'penaltyId'			=> self::NULL_INT,
	);
	protected $tables = array(
		'' => 'switches_port',
	);
	protected $valids = array(
	);
	protected $pk = 'id';
}
