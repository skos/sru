<?php

/**
 * wyciagniecie historii urzadzenia
 */
class UFmap_SruAdmin_DeviceHistory_List extends UFmap {

	protected $columns = array(
	    'id' => 'd.id',
	    'deviceId' => 'device_id',
	    'inoperational' => 'd.inoperational',
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
	);
	protected $columnTypes = array(
	    'id' => self::INT,
	    'deviceId' => self::INT,
	    'inoperational' => self::BOOL,
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
	);
	protected $tables = array(
	    'd' => 'devices_history',
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
