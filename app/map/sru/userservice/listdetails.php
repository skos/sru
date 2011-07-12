<?php
/**
 * wyciagniecie usług usera ze szczegółami dotyczącymi modyfikacji
 */
class UFmap_Sru_UserService_ListDetails
extends UFmap {

	protected $columns = array(
		'id'			=> 'h.id',
		'modifiedAt'	=> 'h.modified_at',
		'userId'		=> 'h.user_id',
		'servId'		=> 'h.serv_id',
		'servType'		=> 'h.serv_type_id',
		'modifiedById'	=> 'h.modified_by',
		'modifiedBy'	=> 'a.name',
		'type'			=> 'h.active',
		'servNameId'	=> 't.id',
		'servName'		=> 't.name',
		'userName'		=> 'u.name',
		'userSurname'	=> 'u.surname',
		'login'			=> 'u.login',
		'userActive'	=> 'u.active',
	);
	protected $columnTypes = array(
		'id'			=> self::INT,
		'modifiedAt'	=> self::TS,
		'userId'		=> self::INT,
		'servId'		=> self::INT,
		'servType'		=> self::INT,
		'modifiedById'	=> self::INT,
		'modifiedBy'	=> self::TEXT,
		'type'			=> self::INT,
		'servNameId'	=> self::INT,
		'servName'		=> self::TEXT,
		'userName'		=> self::TEXT,
		'userSurname' 	=> self::TEXT,
		'login'			=> self::TEXT,
		'userActive'	=> self::BOOL,
	);
	protected $tables = array(
		'h' => 'services_history'
	);
	protected $joins = array(
		't' => 'services_type',
		'u' => 'users',
		'a'	=> 'admins'
	);
	protected $joinOns = array(
		'u' => 'h.user_id=u.id',
		't'	=> 'h.serv_type_id=t.id',
		'a'	=> 'h.modified_by=a.id'
	);
	protected $pk = 'h.id';
}
