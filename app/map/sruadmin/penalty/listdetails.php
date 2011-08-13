<?php
/**
 * wyciagniecie kar
 */
class UFmap_SruAdmin_Penalty_ListDetails
extends UFmap_SruAdmin_Penalty_Get {

	protected $columns = array(
		'id'           	=> 'id',
		'userid'       	=> 'userid',
		'typeid'       	=> 'typeid',
		'endat'        	=> 'endat',
		'modifiedby'   	=> 'modifiedby',
		'modifiedat'   	=> 'modifiedat',
		'template'	   	=> 'template',
		'name'		   	=> 'name',
		'surname'		=> 'surname',
		'login'    		=> 'login',
		'active'   		=> 'active',
		'banned'   		=> 'banned',
		'modifiername'	=> 'modifiername',
		'userdormalias' => 'userdormalias',
		'modificationcount' => 'modificationcount',
	);
	protected $columnTypes = array(
		'id'           	=> self::INT,
		'userid'       	=> self::INT,
		'typeid'       	=> self::INT,
		'endat'        	=> self::TS,
		'modifiedby'	=> self::NULL_INT, 
		'modifiedat'   	=> self::NULL_TS,
		'template'		=> self::TEXT,
		'name'    	   	=> self::TEXT,
		'surname'      	=> self::TEXT,
		'login' 	   	=> self::TEXT,
		'active'   	   	=> self::BOOL,
		'banned'       	=> self::BOOL,
		'modifiername'	=> self::TEXT,
		'userdormalias' => self::TEXT,
		'modificationcount' => self::INT,
	);
}

