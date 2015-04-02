<?
/**
 * edycja komputera
 */
class UFmap_Sru_Computer_Set
extends UFmap_Sru_Computer_Add {

	protected $columns = array(
		'host'           => 'host',
		'mac'            => 'mac',
		'ip'             => 'ipv4',
		'userId'         => 'user_id',
		'locationId'     => 'location_id',
		'availableTo'    => 'avail_to',
		'modifiedById'   => 'modified_by',
		'modifiedAt'     => 'modified_at',
		'comment'        => 'comment',
		'active'         => 'active',
		'typeId'         => 'type_id',
		'canAdmin'       => 'can_admin',
		'exAdmin'        => 'exadmin',
		'lastSeen'		 => 'last_seen',
		'lastActivated'	 => 'last_activated',
		'carerId'		 => 'carer_id',
		'masterHostId'	 => 'master_host_id',
		'autoDeactivation' => 'auto_deactivation',
		'inventoryCardId'=> 'inventory_card_id',
		'deviceModelId' => 'device_model_id',
	);
	protected $columnTypes = array(
		'host'           => self::TEXT,
		'mac'            => self::TEXT,
		'ip'             => self::TEXT,
		'userId'         => self::NULL_INT,
		'locationId'     => self::INT,
		'locationAlias'  => self::TEXT,	// kolumna tylko do walidacji
		'dormitory'      => self::TEXT,	// kolumna tylko do walidacji
		'availableTo'    => self::NULL_TS,
		'modifiedById'   => self::NULL_INT,
		'modifiedAt'     => self::TS,
		'comment'        => self::TEXT,
		'active'         => self::BOOL,
		'typeId'         => self::INT,
		'canAdmin'       => self::BOOL,
		'exAdmin'        => self::BOOL,
		'lastSeen'	 => self::TS,
		'lastActivated'	 => self::TS,
		'carerId'	 => self::NULL_INT,
		'skosCarerId'	 => self::NULL_INT,	// kolumna tylko do walidacji
		'waletCarerId'	 => self::NULL_INT,	// kolumna tylko do walidacji
		'masterHostId'	 => self::NULL_INT,
		'autoDeactivation' => self::BOOL,
		'inventoryCardId'=> self::NULL_INT,
		'deviceModelId'	 => self::NULL_INT,
	);
	protected $tables = array(
		'' => 'computers',
	);
	protected $valids = array(
		'host' => array('textMin'=>1, 'textMax'=>50, 'regexp'=>'^[a-z][-a-z0-9]*[a-z0-9]$'),
		'mac' => array('regexp'=>'^[0-9a-fA-F]{2}?([- :]?[0-9a-fA-F]{2}){5}$'),
		'ip' => array('regexp'=>'^[0-9.]{7,15}$'),
		'locationId' => array('intMin'=>1),
		'locationAlias' => array('textMin'=>1),
		'dormitory' => array('textMin'=>1),
	);
	protected $pk = 'id';
}
