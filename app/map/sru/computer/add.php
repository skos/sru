<?
/**
 * dodanie komputera
 */
class UFmap_Sru_Computer_Add
extends UFmap {

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
		'typeId'         => 'type_id',
		'carerId'		 => 'carer_id',
		'masterHostId'	 => 'master_host_id',
		'autoDeactivation' => 'auto_deactivation',
	    'deviceModelId' => 'device_model_id',
	    'computerId'	=> 'id',
	);
	protected $columnTypes = array(
		'host'           => self::TEXT,
		'mac'            => self::TEXT,
		'ip'             => self::TEXT,
		'userId'         => self::NULL_INT,
		'locationId'     => self::INT,
		'locationAlias'  => self::TEXT,	// kolumna tylko do walidacji
		'availableTo'    => self::NULL_TS,
		'modifiedById'   => self::NULL_INT,
		'modifiedAt'     => self::TS,
		'comment'        => self::TEXT,
		'typeId'         => self::INT,
		'carerId'	 => self::NULL_INT,
		'skosCarerId'	 => self::NULL_INT,	// kolumna tylko do walidacji
		'waletCarerId'	 => self::NULL_INT,	// kolumna tylko do walidacji
		'masterHostId'	 => self::NULL_INT,
		'autoDeactivation' => self::BOOL,
		'deviceModelId'	 => self::NULL_INT,
		'computerId'	=> self::INT,
	);
	protected $tables = array(
		'' => 'computers',
	);
	protected $valids = array(
		'host' => array('textMin'=>1, 'textMax'=>50, 'regexp'=>'^[a-z][-a-z0-9]*[a-z0-9]$'),
		'mac' => array('regexp'=>'^[0-9a-fA-F]{2}?([- :]?[0-9a-fA-F]{2}){5}$'),
		'ip' => array('regexp'=>'^[0-9]{1,3}(\.[0-9]{1,3}){3}$'),
		'locationId' => array('intMin'=>1),
		'locationAlias' => array('textMin'=>1),
	);
	protected $pk = 'id';
}
