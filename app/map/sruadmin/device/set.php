<?php

/**
 * aktualizacja urzadzenia
 */
class UFmap_SruAdmin_Device_Set extends UFmap {

	protected $columns = array(
	    'id' => 'id',
	    'inoperational' => 'inoperational',
	    'modifiedById' => 'modified_by',
	    'modifiedAt' => 'modified_at',
	    'deviceModelId' => 'device_model_id',
	    'locationId' => 'location_id',
	    'comment' => 'comment',
	    'inventoryCardId' => 'inventory_card_id',
	);
	protected $columnTypes = array(
	    'id' => self::INT,
	    'inoperational' => self::BOOL,
	    'modifiedById' => self::NULL_INT,
	    'modifiedAt' => self::TS,
	    'deviceModelId' => self::INT,
	    'locationId' => self::INT,
	    'locationAlias' => self::TEXT,	// kolumna tylko do walidacji
	    'dormitory'     => self::TEXT,	// kolumna tylko do walidacji
	    'comment' => self::NULL_TEXT,
	    'inventoryCardId' => self::INT,
	);
	protected $tables = array(
	    '' => 'devices',
	);
	protected $valids = array(
		'locationId' => array('intMin'=>1),
		'locationAlias' => array('textMin'=>1),
		'dormitory' => array('textMin'=>1),
	);
	protected $pk = 'id';

}
