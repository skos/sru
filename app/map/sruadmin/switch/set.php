<?php
/**
 * aktualizacja switcha
 */
class UFmap_SruAdmin_Switch_Set
extends UFmap {

	protected $columns = array(
		'id'		=> 'id',
		'modifiedById'  => 'modified_by',
		'modifiedAt'    => 'modified_at',
		'hierarchyNo'	=> 'hierarchy_no',
		'modelId'	=> 'model',
		'inoperational'	=> 'inoperational',
		'locationId'    => 'location_id',
		'comment'	=> 'comment',
		'ip'		=> 'ipv4',
		'lab'		=> 'lab',
		'inventoryCardId'=> 'inventory_card_id',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'modifiedById'  => self::NULL_INT,
		'modifiedAt'    => self::TS,
		'hierarchyNo'	=> self::NULL_INT,
		'modelId'	=> self::INT,
		'inoperational'	=> self::BOOL,
		'locationId'    => self::INT,
		'locationAlias' => self::TEXT,	// kolumna tylko do walidacji
		'dormitory'     => self::TEXT,	// kolumna tylko do walidacji
		'comment'	=> self::NULL_TEXT,
		'ip'		=> self::NULL_TEXT,
		'lab'		=> self::BOOL,
		'inventoryCardId'=> self::INT,
	);
	protected $tables = array(
		'' => 'switches',
	);
	protected $valids = array(
		'locationId' => array('intMin'=>1),
		'locationAlias' => array('textMin'=>1),
		'dormitory' => array('textMin'=>1),
	);
	protected $pk = 'id';
}
