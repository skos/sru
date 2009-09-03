<?
/**
 * wyciaganie listy historii uslugi
 */
class UFmap_SruAdmin_ServiceHistory_List
extends UFmap {

	protected $columns = array(
		'userId'	 => 'user_id',
		'adminId'        => 'admin_id',
		'admin'          => 'admin',
		'servName'       => 'serv_name',
		'modifiedAt'     => 'modified_at',
		'state'          => 'active',
	);
	protected $columnTypes = array(
		'userId'         => self::INT,
		'adminId'        => self::NULL_INT,
		'admin'          => self::TEXT,
		'servName'       => self::TEXT,
		'modifiedAt'     => self::TS,
		'state'          => self::INT
	);
	protected $tables = array(
		'' => 'services_history_view',
	);
}
