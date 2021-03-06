<?php
/**
 * wyciagniecie karty wyposazenia
 */
class UFmap_SruAdmin_InventoryCard_Get
extends UFmap {

	protected $columns = array(
		'id'		=> 'i.id',
	    	'modifiedById'   => 'i.modified_by',
		'modifiedBy'     => 'a.name',
		'modifiedAt'     => 'i.modified_at',
		'dormitoryId'	=> 'i.dormitory_id',
		'dormitoryName'	=> 'd.name',
		'dormitoryAlias'=> 'd.alias',
		'serialNo'	=> 'i.serial_no',
		'inventoryNo'	=> 'i.inventory_no',
		'received'	=> 'i.received',
		'comment'	=> 'i.comment',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'modifiedById'   => self::NULL_INT,
		'modifiedBy'     => self::TEXT,
		'modifiedAt'     => self::TS,
		'dormitoryId'	=> self::INT,
		'dormitoryName'	=> self::TEXT,
		'dormitoryAlias'=> self::TEXT,
		'serialNo'	=> self::TEXT,
		'inventoryNo'	=> self::NULL_TEXT,
		'received'	=> self::NULL_TS,
		'comment'	=> self::NULL_TEXT,
	);
	protected $tables = array(
		'i' => 'inventory_cards',
	);
	protected $joins = array(
		'd' => 'dormitories',
		'a' => 'admins',
	);	
	protected $joinOns = array(
		'd' => 'i.dormitory_id=d.id',
		'a' => 'i.modified_by=a.id',
	);
	protected $pk = 'id';
}
