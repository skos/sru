<?
/**
 * wyciaganie listy historii uslugi
 */
class UFmap_SruAdmin_ServiceHistory_List
extends UFmap {

	protected $columns = array(
		'id'             => 'h.id',
		'userId'		 => 'h.user_id',
		'adminId'        => 'h.modified_by',
		'admin'          => 'a.name',
		'servName'       => 't.name',
		'modifiedAt'     => 'h.modified_at',
		'state'          => 'h.active',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'userId'         => self::INT,
		'adminId'        => self::NULL_INT,
		'admin'          => self::TEXT,
		'servName'       => self::TEXT,
		'modifiedAt'     => self::TS,
		'state'          => self::INT
	);
	protected $tables = array(
		'h' => 'services_history',
	);
	protected $joins = array(
		'a' => 'admins',
		't' => 'services_type',
	);
	protected $joinOns = array(
		'a' => 'h.modified_by = a.id',
		't' => 'h.serv_type_id = t.id',
	);
	protected $pk = 'h.id';
}
