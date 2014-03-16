<?php

/**
 * wyciagniecie pojedynczego urzadzenia
 */
class UFmap_SruAdmin_Device_Get extends UFmap {

	protected $columns = array(
	    'id' => 'd.id',
	    'modifiedById' => 'd.modified_by',
	    'modifiedBy' => 'a.name',
	    'modifiedAt' => 'd.modified_at',
	    'typeId' => 'd.type_id',
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
	    'modifiedById' => self::NULL_INT,
	    'modifiedBy' => self::TEXT,
	    'modifiedAt' => self::TS,
	    'typeId' => self::INT,
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
	);
	protected $joinOns = array(
	    'l' => 'd.location_id=l.id',
	    'o' => 'l.dormitory_id=o.id',
	    'a' => 'd.modified_by=a.id',
	);
	protected $pk = 'id';

}
