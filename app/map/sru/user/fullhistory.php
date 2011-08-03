<?
/**
 * mapping do surowego zapytania SQL dla ostatnich akcji admina - użytkownicy
 */
class UFmap_Sru_User_FullHistory
extends UFmap {

	protected $columns = array(
		'id'     		 => 'id',
		'host'           => 'host',
		'userid'		 => 'userid',
		'modifiedat'     => 'modifiedat',
		'active'         => 'active',
		'banned'         => 'banned',
		'name'			 => 'name',
		'surname'		 => 'surname',
		'active'		 => 'active',
		'login'			 => 'login',
		
	);
	protected $columnTypes = array(
		'id'    		 => self::INT,
		'host'           => self::TEXT,
		'userid'		 => self::INT,
		'modifiedby'     => self::NULL_INT,
		'modifiedat'     => self::TS,
		'active'         => self::BOOL,
		'banned'         => self::BOOL,
		'name'			 => self::TEXT,
		'surname'		 => self::TEXT,
		'active'		 => self::BOOL,
		'login'			 => self::TEXT,
	);
}
