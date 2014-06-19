<?php
/**
 * dodanie karty wyposazenia
 */
class UFmap_SruAdmin_InventoryCard_Add
extends UFmap {

	protected $columns = array(
		'id'		=> 'id',
	    	'modifiedById'  => 'modified_by',
		'modifiedAt'    => 'modified_at',
		'serialNo'	=> 'serial_no',
		'inventoryNo'	=> 'inventory_no',
		'received'	=> 'received',
		'dormitoryId'   => 'dormitory_id',
		'comment'	=> 'comment',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'modifiedById'  => self::NULL_INT,
		'modifiedAt'    => self::TS,
		'serialNo'	=> self::TEXT,
		'inventoryNo'	=> self::NULL_TEXT,
		'received'	=> self::NULL_TS,
		'dormitoryId'    => self::INT,
		'comment'	=> self::NULL_TEXT,
	);
	protected $tables = array(
		'' => 'inventory_cards',
	);
	protected $valids = array(
		'serialNo' => array('textMin'=>1, 'textMax'=>100, 'regexp'=>'^[-a-zA-Z0-9\.@_\/]+$'),
		'dormitoryId' => array('intMin'=>1),
	);
	protected $pk = 'id';
}
