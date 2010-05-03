<?php
/**
 * wyciagniecie pojedynczego switcha
 */
class UFmap_SruAdmin_Switch_Get
extends UFmap {

	protected $columns = array(
		'id'		=> 's.id',
		'hierarchyNo'	=> 's.hierarchy_no',
		'modelId'	=> 's.model',
		'model'		=> 'm.model_name',
		'modelNo'	=> 'm.model_no',
		'dormitoryId'	=> 's.dormitory',
		'dormitoryName'	=> 'd.name',
		'dormitoryAlias'=> 'd.alias',
		'serialNo'	=> 's.serial_no',
		'inventoryNo'	=> 's.inventory_no',
		'received'	=> 's.received',
		'operational'	=> 's.operational',
		'localization'	=> 's.localization',
		'comment'	=> 's.comment',
		'ip'		=> 's.ipv4',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'hierarchyNo'	=> self::NULL_INT,
		'modelId'	=> self::INT,
		'model'		=> self::TEXT,
		'modelNo'	=> self::TEXT,
		'dormitoryId'	=> self::INT,
		'dormitoryName'	=> self::TEXT,
		'dormitoryAlias'=> self::TEXT,
		'serialNo'	=> self::TEXT,
		'inventoryNo'	=> self::NULL_TEXT,
		'received'	=> self::NULL_TS,
		'operational'	=> self::BOOL,
		'localization'	=> self::NULL_TEXT,
		'comment'	=> self::NULL_TEXT,
		'ip'		=> self::NULL_TEXT,
	);
	protected $tables = array(
		's' => 'switches',
	);
	protected $joins = array(
		'm' => 'switches_model',
		'd' => 'dormitories',
	);	
	protected $joinOns = array(
		'm' => 's.model=m.id',
		'd' => 's.dormitory=d.id',
	);
	protected $pk = 'id';
}
