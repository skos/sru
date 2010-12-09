<?
/**
 * wyszukwanie uzytkownikow
 */
class UFmap_Sru_User_Search
extends UFmap {

	protected $columns = array(
		'id'             => 'u.id',
		'login'          => 'u.login',
		'loginSearch'    => 'lower(u.login)',
		'name'           => 'u.name',
		'nameSearch'     => 'lower(u.name)',
		'surname'        => 'u.surname',
		'surnameSearch'  => 'lower(u.surname)',
		'email'			 => 'email',
		'emailSearch'	 => 'lower(u.email)',
		'locationId'     => 'u.location_id',
		'locationAlias'  => 'l.alias',
		'room'			 => 'l.alias',
		'roomSearch'	 => 'lower(l.alias)',
		'dormitoryId'    => 'l.dormitory_id',
		'dormitoryAlias' => 'd.alias',
		'dormitoryName'  => 'd.name',
		'dormitory'      => 'd.alias',
		'active'         => 'u.active',
		'registryNo'	 => 'u.registry_no',
		'referralStart'	 => 'u.referral_start',
		'banned'         => 'u.banned',
		'comment'        => 'u.comment',
		'locationComment'=> 'l.comment',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'login'          => self::TEXT,
		'loginSearch'    => self::TEXT,
		'name'           => self::TEXT,
		'nameSearch'     => self::TEXT,
		'surname'        => self::TEXT,
		'surnameSearch'  => self::TEXT,
		'email'			 => self::TEXT,
		'emailSearch'	 => self::TEXT,
		'locationId'     => self::INT,
		'locationAlias'  => self::TEXT,
		'room'		     => self::TEXT,
		'roomSearch'	 => self::TEXT,
		'dormitoryId'    => self::INT,
		'dormitoryAlias' => self::TEXT,
		'dormitoryName'  => self::TEXT,
		'dormitory'      => self::TEXT,
		'active'         => self::BOOL,
		'registryNo'	 => self::NULL_INT,
		'referralStart'	 => self::TS,
		'banned'         => self::BOOL,
		'comment'        => self::TEXT,
		'locationComment'=> self::TEXT,
	);
	protected $tables = array(
		'u' => 'users',
	);
	protected $joins = array(
		'f' => 'faculties',
		'l' => 'locations',
		'd' => 'dormitories',
	);
	protected $joinOns = array(
		'f' => 'u.faculty_id=f.id',
		'l' => 'u.location_id=l.id',
		'd' => 'l.dormitory_id=d.id',
	);
	protected $pk = 'u.id';
}
