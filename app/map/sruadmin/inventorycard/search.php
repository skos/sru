<?
/**
 * wyszukiwanie urzadzen
 */
class UFmap_SruAdmin_InventoryCard_Search
extends UFmap {
		protected $columns = array(
		'cardId'		=> 'i.card_id',
		'deviceId'		=> 'i.id',
		'cardDormitoryId'	=> 'i.ic_dormitory_id',
		'cardDormitoryName'	=> 'cd.name',
		'cardDormitoryAlias'	=> 'cd.alias',
		'dormitory'		=> 'cd.alias',
		'dormitoryId'		=> 'i.dormitory_id',
		'dormitoryName'		=> 'd.name',
		'dormitoryAlias'	=> 'd.alias',
	    	'locationId'		=> 'i.location_id',
		'locationAlias'		=> 'l.alias',
		'serialNo'		=> 'i.serial_no',
		'serialNoSearch'	=> 'lower(i.serial_no)',
		'inventoryNo'		=> 'i.inventory_no',
		'inventoryNoSearch'	=> 'lower(i.inventory_no)',
		'received'		=> 'i.received',
		'deviceModelId'		=> 'i.device_model_id',
		'deviceModelName'	=> 'i.device_model_name',
		'deviceTableId'		=> 'i.table_id',
	);
	protected $columnTypes = array(
		'cardId'		=> self::INT,
		'deviceId'		=> self::INT,
		'cardDormitoryId'	=> self::INT,
		'cardDormitoryName'	=> self::TEXT,
		'cardDormitoryAlias'	=> self::TEXT,
		'dormitory'		=> self::TEXT,
		'dormitoryId'		=> self::INT,
		'dormitoryName'		=> self::TEXT,
		'dormitoryAlias'	=> self::TEXT,
		'locationId'		=> self::INT,
		'locationAlias'		=> self::TEXT,
		'serialNo'		=> self::TEXT,
		'serialNoSearch'	=> self::TEXT,
		'inventoryNo'		=> self::TEXT,
		'inventoryNoSearch'	=> self::TEXT,
		'received'		=> self::NULL_TS,
		'deviceModelId'		=> self::INT,
		'deviceModelName'	=> self::TEXT,
		'deviceTableId'		=> self::INT,
	);
	protected $tables = array(
		'i' => 'v_inventory_list',
	);
	protected $joins = array(
		'cd' => 'dormitories',
		'd' => 'dormitories',
		'l' => 'locations',
	);	
	protected $joinOns = array(
		'cd' => 'i.ic_dormitory_id=cd.id',
		'd' => 'i.dormitory_id=d.id',
		'l' => 'i.location_id=l.id',
	);
	protected $pk = 'cardId';
}
