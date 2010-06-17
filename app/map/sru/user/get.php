<?
/**
 * wyciagniecie pojedynczego uzytkownika
 */
class UFmap_Sru_User_Get
extends UFmap {

	protected $columns = array(
		'id'             => 'u.id',
		'login'          => 'u.login',
		'password'       => 'u.password',
		'name'           => 'u.name',
		'surname'        => 'u.surname',
		'email'          => 'u.email',
		'gg'             => 'u.gg',
		'facultyId'      => 'u.faculty_id',
		'facultyName'    => 'f.name',
		'facultyAlias'   => 'f.alias',
		'studyYearId'    => 'u.study_year_id',
		'locationId'     => 'u.location_id',
		'locationAlias'  => 'l.alias',
		'dormitoryId'    => 'l.dormitory_id',
		'dormitoryAlias' => 'd.alias',
		'dormitoryName'  => 'd.name',
		'bans'           => 'u.bans',
		'modifiedById'   => 'u.modified_by',
		'modifiedBy'     => 'a.name',
		'modifiedAt'     => 'u.modified_at',
		'comment'        => 'u.comment',
		'active'         => 'u.active',
		'banned'         => 'u.banned',
		'lastLoginAt'    => 'u.last_login_at',
		'lastLoginIp'    => 'u.last_login_ip',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'login'          => self::TEXT,
		'password'       => self::TEXT,
		'name'           => self::TEXT,
		'surname'        => self::TEXT,
		'email'          => self::TEXT,
		'gg'             => self::TEXT,
		'facultyId'      => self::NULL_INT,
		'facultyName'    => self::TEXT,
		'facultyAlias'   => self::TEXT,
		'studyYearId'    => self::NULL_INT,
		'locationId'     => self::INT,
		'locationAlias'  => self::TEXT,
		'dormitoryId'    => self::INT,
		'dormitoryAlias' => self::TEXT,
		'dormitoryName'  => self::TEXT,
		'bans'           => self::INT,
		'modifiedById'   => self::NULL_INT,
		'modifiedBy'     => self::TEXT,
		'modifiedAt'     => self::TS,
		'comment'        => self::TEXT,
		'active'         => self::BOOL,
		'banned'         => self::BOOL,
		'lastLoginAt'    => self::NULL_TS,
		'lastLoginIp'    => self::NULL_TEXT,
	);
	protected $tables = array(
		'u' => 'users',
	);
	protected $joins = array(
		'f' => 'faculties',
		'l' => 'locations',
		'd' => 'dormitories',
		'a' => 'admins',
	);
	protected $joinOns = array(
		'f' => 'u.faculty_id=f.id',
		'l' => 'u.location_id=l.id',
		'd' => 'l.dormitory_id=d.id',
		'a' => 'u.modified_by=a.id',
	);
	protected $pk = 'u.id';
}
