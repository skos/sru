<?
/**
 * modyfikacja uzytkownika
 */
class UFmap_Sru_User_Set
extends UFmap {

	protected $columns = array(
		'login'          => 'login',
		'password'       => 'password',
		'typeId'	 => 'type_id',
		'name'           => 'name',
		'surname'        => 'surname',
		'email'          => 'email',
		'gg'             => 'gg',
		'facultyId'      => 'faculty_id',
		'studyYearId'    => 'study_year_id',
		'locationId'     => 'location_id',
		'modifiedById'   => 'modified_by',
		'modifiedAt'     => 'modified_at',
		'comment'        => 'comment',
		'active'         => 'active',
		'lastLoginAt'	 => 'last_login_at',
		'lastLoginIp'	 => 'last_login_ip',
		'lastInvLoginAt' => 'last_inv_login_at',
		'lastInvLoginIp' => 'last_inv_login_ip',
		'lang'			 => 'lang',
		'referralStart'	 => 'referral_start',
		'referralEnd'	 => 'referral_end',
		'registryNo'	 => 'registry_no',
		'updateNeeded'	 => 'update_needed',
		'changePasswordNeeded'	=> 'change_password_needed',
		'servicesAvailable'	=> 'services_available',
	);
	protected $columnTypes = array(
		'login'          => self::TEXT,
		'password'       => self::TEXT,
		'typeId'	 => self::INT,
		'name'           => self::TEXT,
		'surname'        => self::TEXT,
		'email'          => self::TEXT,
		'gg'             => self::TEXT,
		'facultyId'      => self::NULL_INT,
		'studyYearId'    => self::NULL_INT,
		'dormitory'      => self::TEXT,	// kolumna tylko do walidacji
		'locationId'     => self::INT,
		'locationAlias'  => self::TEXT,	// kolumna tylko do walidacji
		'modifiedById'   => self::NULL_INT,
		'modifiedAt'     => self::TS,
		'comment'        => self::TEXT,
		'active'         => self::BOOL,
		'lastLoginAt'	 => self::TS,
		'lastLoginIp'	 => self::TEXT,
		'lastInvLoginAt' => self::TS,
		'lastInvLoginIp' => self::TEXT,
		'lang'           => self::TEXT,
		'referralStart'	 => self::TS,
		'referralEnd'	 => self::TS,
		'registryNo'	 => self::NULL_INT,
		'updateNeeded'	 => self::BOOL,
		'changePasswordNeeded'	=> self::BOOL,
		'servicesAvailable'	=> self::BOOL,
	);
	protected $tables = array(
		'' => 'users',
	);
	protected $valids = array(
		'login' => array('textMin'=>1, 'textMax'=>100, 'regexp'=>'^[-a-zA-Z0-9\.@_]+$'),
		'password' => array('textMin'=>6),
		'name' => array('textMin'=>1, 'textMax'=>100),
		'surname' => array('textMin'=>1, 'textMax'=>100),
		'email' => array('email'=>true, 'textMax'=>100),
		'gg' => array('regexp'=>'^(|[0-9]{5,50})$'),
		'facultyId' => array('textMin'=>1, 'regexp'=>'^(1|2|3|4|5|6|7|8|9|0)+$'),
		'studyYearId' => array('textMin'=>1, 'regexp'=>'^(1|2|3|4|5|6|7|8|9|10|11|0)+$'),
		'dormitory' => array('textMin'=>1),
		'locationAlias' => array('textMin'=>1),
		'locationId' => array('intMin'=>1),
		'registryNo' => array('regexp'=>'^(|[0-9]{5,6})$'),
	);
	protected $pk = 'id';
}
