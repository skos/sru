<?php
/**
 * wyciagniecie pojedynczego switcha
 */
class UFmap_SruAdmin_Switch_Get
extends UFmap {

	protected $columns = array(
		'id'		=> 's.id',
	    	'modifiedById'   => 's.modified_by',
		'modifiedBy'     => 'a.name',
		'modifiedAt'     => 's.modified_at',
		'hierarchyNo'	=> 's.hierarchy_no',
		'modelId'	=> 's.model',
		'model'		=> 'm.model_name',
		'modelNo'	=> 'm.model_no',
		'modelPorts'	=> 'm.ports_no',
		'modelSfpPorts'	=> 'm.sfp_ports_no',
		'modelFirmware' => 'f.firmware',
		'dormitoryId'	=> 'l.dormitory_id',
		'dormitoryName'	=> 'd.name',
		'dormitoryAlias'=> 'd.alias',
		'locationId'     => 's.location_id',
		'locationAlias'  => 'l.alias',
		'locationComment'=> 'l.comment',
		'displayOrder' =>  'd.display_order',
		'serialNo'	=> 'i.serial_no',
		'inoperational'	=> 's.inoperational',
		'comment'	=> 's.comment',
		'ip'		=> 's.ipv4',
		'lab'		=> 's.lab',
		'inventoryCardId'=> 's.inventory_card_id',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
	    	'modifiedById'   => self::NULL_INT,
		'modifiedBy'     => self::TEXT,
		'modifiedAt'     => self::TS,
		'hierarchyNo'	=> self::NULL_INT,
		'modelId'	=> self::INT,
		'model'		=> self::TEXT,
		'modelNo'	=> self::TEXT,
		'modelPorts'	=> self::INT,
		'modelSfpPorts'	=> self::INT,
		'modelFirmware' => self::TEXT,
		'dormitoryId'	=> self::INT,
		'dormitoryName'	=> self::TEXT,
		'dormitoryAlias'=> self::TEXT,
	    	'locationId'     => self::INT,
		'locationAlias'  => self::TEXT,
		'locationComment'=> self::TEXT,
		'displayOrder'	=> self::INT,
		'serialNo'	=> self::TEXT,
		'inoperational'	=> self::BOOL,
		'comment'	=> self::NULL_TEXT,
		'ip'		=> self::NULL_TEXT,
		'lab'		=> self::BOOL,
		'inventoryCardId'=> self::INT,
	);
	protected $tables = array(
		's' => 'switches',
	);
	protected $joins = array(
		'm' => 'switches_model',
		'f' => 'switches_firmware',
		'l' => 'locations',
		'd' => 'dormitories',
		'i' => 'inventory_cards',
		'a' => 'admins',
		'id' => 'dormitories',
	);	
	protected $joinOns = array(
		'm' => 's.model=m.id',
		'f' => 'm.firmware_id=f.id',
		'l' => 's.location_id=l.id',
		'd' => 'l.dormitory_id=d.id',
		'i' => 's.inventory_card_id=i.id',
		'a' => 's.modified_by=a.id',
		'id' => 'i.dormitory_id=id.id',
	);
	protected $pk = 'id';
}
