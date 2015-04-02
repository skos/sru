<?php

/**
 * wyciagniecie listy zmian switcha
 */
class UFmap_SruAdmin_SwitchHistory_List extends UFmap {

	protected $columns = array(
	    'id' => 's.id',
	    'switchId' => 's.switch_id',
	    'modifiedById' => 's.modified_by',
	    'modifiedBy' => 'a.name',
	    'modifiedAt' => 's.modified_at',
	    'hierarchyNo' => 's.hierarchy_no',
	    'modelId' => 's.model',
	    'model' => 'm.model_name',
	    'dormitoryId' => 'l.dormitory_id',
	    'dormitoryName' => 'd.name',
	    'dormitoryAlias' => 'd.alias',
	    'locationId' => 's.location_id',
	    'locationAlias' => 'l.alias',
	    'inoperational' => 's.inoperational',
	    'comment' => 's.comment',
	    'ip' => 's.ipv4',
	);
	protected $columnTypes = array(
	    'id' => self::INT,
	    'switchId' => self::INT,
	    'modifiedById' => self::NULL_INT,
	    'modifiedBy' => self::TEXT,
	    'modifiedAt' => self::TS,
	    'hierarchyNo' => self::NULL_INT,
	    'modelId' => self::INT,
	    'model' => self::TEXT,
	    'dormitoryId' => self::INT,
	    'dormitoryName' => self::TEXT,
	    'dormitoryAlias' => self::TEXT,
	    'locationId' => self::INT,
	    'locationAlias' => self::TEXT,
	    'inoperational' => self::BOOL,
	    'comment' => self::NULL_TEXT,
	    'ip' => self::NULL_TEXT,
	);
	protected $tables = array(
	    's' => 'switches_history',
	);
	protected $joins = array(
	    'a' => 'admins',
	    'm' => 'switches_model',
	    'l' => 'locations',
	    'd' => 'dormitories',
	);
	protected $joinOns = array(
	    'a' => 's.modified_by=a.id',
	    'm' => 's.model=m.id',
	    'l' => 's.location_id=l.id',
	    'd' => 'l.dormitory_id=d.id',
	);
	protected $pk = 'id';

}
