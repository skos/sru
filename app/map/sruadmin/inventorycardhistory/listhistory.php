<?php

/**
 * wyciagniecie hitorii karty wyposazenia
 */
class UFmap_SruAdmin_InventoryCardHistory_ListHistory extends UFmap {

	protected $columns = array(
	    'id' => 'i.id',
	    'inventoryCardId' => 'i.inventory_card_id',
	    'modifiedById' => 'i.modified_by',
	    'modifiedBy' => 'a.name',
	    'modifiedAt' => 'i.modified_at',
	    'dormitoryId' => 'i.dormitory_id',
	    'dormitoryName' => 'd.name',
	    'dormitoryAlias' => 'd.alias',
	    'serialNo' => 'i.serial_no',
	    'serialNoLower' => 'lower(i.serial_no)',
	    'inventoryNo' => 'i.inventory_no',
	    'received' => 'i.received',
	    'comment' => 'i.comment',
	    'currentSerialNo' => 'v.serial_no',
	    'deviceId' => 'v.id',
	    'deviceModelName' => 'v.device_model_name',
	    'deviceTableId' => 'v.table_id',
	);
	protected $columnTypes = array(
	    'id' => self::INT,
	    'inventoryCardId' => self::INT,
	    'modifiedById' => self::NULL_INT,
	    'modifiedBy' => self::TEXT,
	    'modifiedAt' => self::TS,
	    'dormitoryId' => self::INT,
	    'dormitoryName' => self::TEXT,
	    'dormitoryAlias' => self::TEXT,
	    'serialNo' => self::TEXT,
	    'serialNoLower' => self::TEXT,
	    'inventoryNo' => self::NULL_TEXT,
	    'received' => self::NULL_TS,
	    'comment' => self::NULL_TEXT,
	    'currentSerialNo' => self::TEXT,
	    'deviceId' => self::INT,
	    'deviceModelName' => self::TEXT,
	    'deviceTableId' => self::INT,
	);
	protected $tables = array(
	    'i' => 'inventory_cards_history',
	);
	protected $joins = array(
	    'd' => 'dormitories',
	    'a' => 'admins',
	    'v' => 'v_inventory_list',
	);
	protected $joinOns = array(
	    'd' => 'i.dormitory_id=d.id',
	    'a' => 'i.modified_by=a.id',
	    'v' => 'i.inventory_card_id=v.card_id',
	);
	protected $pk = 'id';

}
