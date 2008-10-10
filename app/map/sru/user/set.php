<?
/**
 * modyfikacja uzytkownika
 */
class UFmap_Sru_User_Set
extends UFmap {

	protected $columns = array(
		'login'          => 'login',
		'password'       => 'password',
		'name'           => 'name',
		'surname'        => 'surname',
		'email'          => 'email',
		'facultyId'      => 'faculty_id',
		'studyYearId'    => 'study_year_id',
		'locationId'     => 'location_id',
		'modifiedById'   => 'modified_by',
		'modifiedAt'     => 'modified_at',
		'comment'        => 'comment',
		'active'         => 'active',
	);
	protected $columnTypes = array(
		'login'          => self::TEXT,
		'password'       => self::TEXT,
		'name'           => self::TEXT,
		'surname'        => self::TEXT,
		'email'          => self::TEXT,
		'facultyId'      => self::NULL_INT,
		'studyYearId'    => self::NULL_INT,
		'dormitory'      => self::TEXT,	// kolumna tylko do walidacji
		'locationId'     => self::INT,
		'locationAlias'  => self::TEXT,	// kolumna tylko do walidacji
		'modifiedById'   => self::NULL_INT,
		'modifiedAt'     => self::TS,
		'comment'        => self::TEXT,
		'active'         => self::BOOL,
	);
	protected $tables = array(
		'' => 'users',
	);
	protected $valids = array(
		'login' => array('textMin'=>1, 'textMax'=>100, 'regexp'=>'^[-a-zA-Z0-9\.@_]+$'),
		'password' => array('textMin'=>6),
		'name' => array('textMin'=>1),
		'surname' => array('textMin'=>1),
		'email' => array('email'=>true),
		'facultyId' => array('textMin'=>1, 'regexp'=>'^(1|2|3|4|5|6|7|8|9|0)+$'),
		'studyYearId' => array('textMin'=>1, 'regexp'=>'^(1|2|3|4|5|6|7|8|9|10|11|0)+$'),
		'dormitory' => array('textMin'=>1),
		'locationAlias' => array('textMin'=>1),
		'locationId' => array('intMin'=>1),
	);
	protected $pk = 'id';
}
