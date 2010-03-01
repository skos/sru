<?
/**
 * wyciagniecie statsów uzytkownika
 */
class UFmap_Sru_User_Migration
extends UFmap {

	protected $columns = array(
		'id'			=> 'u.id',
		'login'			=> 'u.login',
		'password'		=> 'u.password',
		'name'			=> 'u.name',
		'surname'		=> 'u.surname',
		'email'			=> 'u.email',
		'gg'			=> 'u.gg',
		'facultyId'		=> 'u.faculty_id',
		'studyYearId'		=> 'u.study_year_id',
		'locationId'		=> 'u.location_id',
		'locationAlias'		=> 'l.alias',
		'dormitoryId'		=> 'l.dormitory_id',
		'dormitoryAlias'	=> 'd.alias',
		'dormitoryName'		=> 'd.name',
		'bans'			=> 'u.bans',
		'wrongDataWarnings'	=> '(select count(*) from penalties p where p.user_id = u.id and template_id = 9 and p.end_at > now())',
		'wrongDataBans'		=> '(select count(*) from penalties p where p.user_id = u.id and template_id = 16 and p.active = true)',
		'modifiedById'		=> 'u.modified_by',
		'modifiedAt'		=> 'u.modified_at',
		'comment'		=> 'u.comment',
		'active'		=> 'u.active',
		'banned'		=> 'u.banned',
	);
	protected $columnTypes = array(
		'id'			=> self::INT,
		'login'			=> self::TEXT,
		'password'		=> self::TEXT,
		'name'			=> self::TEXT,
		'surname'		=> self::TEXT,
		'email'			=> self::TEXT,
		'gg'			=> self::TEXT,
		'facultyId'		=> self::NULL_INT,
		'studyYearId'		=> self::NULL_INT,
		'locationId'		=> self::INT,
		'locationAlias'		=> self::TEXT,
		'dormitoryId'		=> self::INT,
		'dormitoryAlias'	=> self::TEXT,
		'dormitoryName'		=> self::TEXT,
		'bans'			=> self::INT,
		'wrongDataWarnings'	=> self::INT,
		'wrongDataBans'		=> self::INT,
		'modifiedById'		=> self::NULL_INT,
		'modifiedAt'		=> self::TS,
		'comment'		=> self::TEXT,
		'active'		=> self::BOOL,
		'banned'		=> self::BOOL,
	);
	protected $tables = array(
		'u' => 'users',
	);
	protected $joins = array(
		'l' => 'locations',
		'd' => 'dormitories',
	);
	protected $joinOns = array(
		'l' => 'u.location_id=l.id',
		'd' => 'l.dormitory_id=d.id',
	);
	protected $pk = 'u.id';
}
