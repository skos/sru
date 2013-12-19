<?php
/**
 * dodanie switcha
 */
class UFmap_SruAdmin_Switch_Add
extends UFmap {

	protected $columns = array(
		'id'		=> 'id',
		'hierarchyNo'	=> 'hierarchy_no',
		'modelId'	=> 'model',
		'serialNo'	=> 'serial_no',
		'inventoryNo'	=> 'inventory_no',
		'received'	=> 'received',
		'inoperational'	=> 'inoperational',
		'locationId'    => 'location_id',
		'comment'	=> 'comment',
		'ip'		=> 'ipv4',
		'lab'		=> 'lab',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'hierarchyNo'	=> self::NULL_INT,
		'modelId'	=> self::INT,
		'serialNo'	=> self::TEXT,
		'inventoryNo'	=> self::NULL_TEXT,
		'received'	=> self::NULL_TS,
		'inoperational'	=> self::BOOL,
		'locationId'     => self::INT,
		'locationAlias'  => self::TEXT,	// kolumna tylko do walidacji
		'comment'	=> self::NULL_TEXT,
		'ip'		=> self::NULL_TEXT,
		'lab'		=> self::BOOL,
	);
	protected $tables = array(
		'' => 'switches',
	);
	protected $valids = array(
		'serialNo' => array('textMin'=>1, 'textMax'=>100, 'regexp'=>'^[-a-zA-Z0-9\.@_]+$'),
		'ip' => array('regexp'=>'^[0-9]{1,3}(\.[0-9]{1,3}){3}$|^$'),
		'locationId' => array('intMin'=>1),
		'locationAlias' => array('textMin'=>1),
	);
	protected $pk = 'id';
}
