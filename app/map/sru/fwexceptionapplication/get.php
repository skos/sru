<?php
/**
 * pobranie wniosku
 */
class UFmap_Sru_FwExceptionApplication_Get
extends UFmap {

	protected $columns = array(
		'id'			=> 'a.id',
		'userId'		=> 'a.user_id',
		'userLogin'		=> 'u.login',
		'userName'		=> 'u.name',
		'userSurname'		=> 'u.surname',
		'validTo'		=> 'a.valid_to',
		'createdAt'		=> 'a.created_at',
		'selfEducation'		=> 'a.self_education',
		'universityEducation'	=> 'a.university_education',
		'comment'		=> 'a.comment',
		'skosOpinion'		=> 'a.skos_opinion',
		'sspgOpinion'		=> 'a.sspg_opinion',
	);
	protected $columnTypes = array(
		'id'			=> self::INT,
		'userId'		=> self::INT,
	    	'userLogin'		=> self::TEXT,
		'userName'		=> self::TEXT,
		'userSurname'		=> self::TEXT,
		'validTo'		=> self::TS,
		'createdAt'		=> self::TS,
		'selfEducation'		=> self::BOOL,
		'universityEducation'	=> self::BOOL,
		'comment'		=> self::TEXT,
		'skosOpinion'		=> self::NULL_BOOL,
		'sspgOpinion'		=> self::NULL_BOOL,
	);
	protected $tables = array(
		'a' => 'fw_exception_applications',
	);
	protected $joins = array(
		'u' => 'users',
	);
	protected $joinOns = array(
		'u' => 'a.user_id=u.id',
	);
	protected $pk = 'a.id';
}

