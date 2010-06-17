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
		'dormitoryId'	=> 'dormitory',
		'serialNo'	=> 'serial_no',
		'inventoryNo'	=> 'inventory_no',
		'received'	=> 'received',
		'inoperational'	=> 'inoperational',
		'localization'	=> 'localization',
		'comment'	=> 'comment',
		'ip'		=> 'ipv4',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'hierarchyNo'	=> self::NULL_INT,
		'modelId'	=> self::INT,
		'dormitoryId'	=> self::INT,
		'serialNo'	=> self::TEXT,
		'inventoryNo'	=> self::NULL_TEXT,
		'received'	=> self::NULL_TS,
		'inoperational'	=> self::BOOL,
		'localization'	=> self::NULL_TEXT,
		'comment'	=> self::NULL_TEXT,
		'ip'		=> self::NULL_TEXT,
	);
	protected $tables = array(
		'' => 'switches',
	);
	protected $valids = array(
		'serialNo' => array('textMin'=>1, 'textMax'=>100, 'regexp'=>'^[-a-zA-Z0-9\.@_]+$'),
		'dormitoryId' => array('textMin'=>1),
		'ip' => array('regexp'=>'^[0-9]{1,3}(\.[0-9]{1,3}){3}$|^$'),
	);
	protected $pk = 'id';
}
