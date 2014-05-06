<?php
/**
 * wyszukiwanie karty wyposazenia
 */
class UFmap_SruAdmin_InventoryCard_InventoryList
extends UFmap {

	protected $columns = array(
		'cardId'	=> 'i.card_id',
		'deviceId'	=> 'i.id',
		'dormitoryId'	=> 'i.dormitory_id',
		'dormitoryName'	=> 'd.name',
		'dormitoryAlias'=> 'd.alias',
	    	'locationId'	=> 'i.location_id',
		'locationAlias'	=> 'l.alias',
		'serialNo'	=> 'i.serial_no',
		'inventoryNo'	=> 'i.inventory_no',
		'received'	=> 'i.received',
		'deviceModelId'	=> 'i.device_model_id',
		'deviceTableId'	=> 'i.table_id',
	);
	protected $columnTypes = array(
		'cardId'	=> self::INT,
		'deviceId'	=> self::INT,
		'dormitoryId'	=> self::INT,
		'dormitoryName'	=> self::TEXT,
		'dormitoryAlias'=> self::TEXT,
		'locationId'	=> self::INT,
		'locationAlias'	=> self::TEXT,
		'serialNo'	=> self::TEXT,
		'inventoryNo'	=> self::NULL_TEXT,
		'received'	=> self::NULL_TS,
		'deviceModelId'	=> self::INT,
		'deviceTableId'	=> self::INT,
	);
	protected $tables = array(
		'i' => 'v_inventory_list',
	);
	protected $joins = array(
		'd' => 'dormitories',
		'l' => 'locations',
	);	
	protected $joinOns = array(
		'd' => 'i.dormitory_id=d.id',
		'l' => 'i.location_id=l.id',
	);
	protected $pk = 'cardId';
}
