<?php

/**
 * wyciagniecie pojedynczego urzadzenia
 */
class UFmap_SruAdmin_Device_Get extends UFmap {

	protected $columns = array(
	    'id' => 'd.id',
	    'inoperational' => 'd.inoperational',
	    'used' => 'd.used',
	    'modifiedById' => 'd.modified_by',
	    'modifiedBy' => 'a.name',
	    'modifiedAt' => 'd.modified_at',
	    'deviceModelId' => 'd.device_model_id',
	    'deviceModelName' => 'm.name',
	    'dormitoryId' => 'l.dormitory_id',
	    'dormitoryName' => 'o.name',
	    'dormitoryAlias' => 'o.alias',
	    'locationId' => 'd.location_id',
	    'locationAlias' => 'l.alias',
	    'locationComment' => 'l.comment',
	    'comment' => 'd.comment',
	    'inventoryCardId' => 'd.inventory_card_id',
	);
	protected $columnTypes = array(
	    'id' => self::INT,
	    'inoperational' => self::BOOL,
	    'used' => self::BOOL,
	    'modifiedById' => self::NULL_INT,
	    'modifiedBy' => self::TEXT,
	    'modifiedAt' => self::TS,
	    'deviceModelId' => self::INT,
	    'deviceModelName' => self::TEXT,
	    'dormitoryId' => self::INT,
	    'dormitoryName' => self::TEXT,
	    'dormitoryAlias' => self::TEXT,
	    'locationId' => self::INT,
	    'locationAlias' => self::TEXT,
	    'locationComment' => self::TEXT,
	    'comment' => self::NULL_TEXT,
	    'inventoryCardId' => self::INT,
	);
	protected $tables = array(
	    'd' => 'devices',
	);
	protected $joins = array(
	    'l' => 'locations',
	    'o' => 'dormitories',
	    'a' => 'admins',
	    'm' => 'device_models',
	);
	protected $joinOns = array(
	    'l' => 'd.location_id=l.id',
	    'o' => 'l.dormitory_id=o.id',
	    'a' => 'd.modified_by=a.id',
	    'm' => 'd.device_model_id = m.id',
	);
	protected $pk = 'id';

}
