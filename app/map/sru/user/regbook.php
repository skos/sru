<?php
/**
 * wyciagniecie listy userow
 */
class UFmap_Sru_User_RegBook
extends UFmap_Sru_User_Get {
	protected $columns = array(
		'id'             => 'u.id',
		'name'           => 'u.name',
		'surname'        => 'u.surname',
		'type_id'	 	 => 'u.type_id',
		'active'         => 'u.active',
		'alias'  => 'l.alias',
		'study_year_id'	 => 'u.study_year_id',
		'faculty_id'      => 'u.faculty_id',
		'faculty_alias'   => 'u.faculty_alias',
		'referral_start'	 => 'u.referral_start',
		'referral_end'	 => 'u.referral_end',
		'registry_no'	 => 'u.registry_no',
		'address'		=> 'u.address',
		'document_type'	=> 'u.document_type',
		'document_number'=> 'u.document_number',
		'nationality'	=> 'u.nationality',
		'birth_date'		=> 'u.birth_date',
		'last_location_change' => 'u.last_location_change',
		'nationality'	=> 'u.nationality',
		'modified_at'	=> 'u.modified_at',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'name'           => self::TEXT,
		'surname'        => self::TEXT,
		'type_id'	 	 => self::INT,
		'active'         => self::BOOL,
		'alias'  => self::TEXT,
		'study_year_id'	 => self::NULL_INT,
		'faculty_id'      => self::NULL_INT,
		'faculty_alias'   => self::TEXT,
		'referral_start'	 => self::TS,
		'referral_end'	 => self::TS,
		'registry_no'	 => self::NULL_INT,
		'address'		=> self::TEXT,
		'document_type'	=> self::INT,
		'document_number'=> self::TEXT,
		'birth_date'		=> self::NULL_TS,
		'last_location_change' => self::TS,
		'nationality'	=> self::INT,
		'modified_at'	=> self::TS,
	);
	protected $tables = array(
		'u' => 'users',
	);
	protected $pk = 'u.id';
}
