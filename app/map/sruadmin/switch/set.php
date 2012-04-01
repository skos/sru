<?php
/**
 * aktualizacja switcha
 */
class UFmap_SruAdmin_Switch_Set
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
		'lab'		=> 'lab',
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
		'lab'		=> self::BOOL,
	);
	protected $tables = array(
		'' => 'switches',
	);
	protected $valids = array(
		'serialNo' => array('textMin'=>1, 'textMax'=>100, 'regexp'=>'^[-a-zA-Z0-9\.@_]+$'),
		'dormitoryId' => array('textMin'=>1),
	);
	protected $pk = 'id';
}
