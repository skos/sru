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
		'availableMaxTo' => 'avail_max_to',
		'modifiedById'   => 'modified_by',
		'modifiedAt'     => 'modified_at',
		'comment'        => 'comment',
		'typeId'         => 'type_id',
	);
	protected $columnTypes = array(
		'host'           => self::TEXT,
		'mac'            => self::TEXT,
		'ip'             => self::TEXT,
		'userId'         => self::NULL_INT,
		'locationId'     => self::INT,
		'availableTo'    => self::TS,
		'availableMaxTo' => self::TS,
		'modifiedById'   => self::NULL_INT,
		'modifiedAt'     => self::TS,
		'comment'        => self::TEXT,
		'typeId'         => self::INT,
	);
	protected $tables = array(
		'' => 'computers',
	);
	protected $valids = array(
		'host' => array('textMin'=>1, 'textMax'=>50, 'regexp'=>'^[a-z][-a-z0-9]*$'),
		'mac' => array('regexp'=>'^[0-9a-fA-F]{1,2}?([- :]?[0-9a-fA-F]{1,2}){5}$'),
		'ip' => array('regexp'=>'^[0-9.]{7,15}$'),
		'locationId' => array('textMin'=>1),
	);
	protected $pk = 'id';
}
