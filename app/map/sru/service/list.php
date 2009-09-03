<?php
/**
 * wyciagniecie usług
 */
class UFmap_Sru_Service_List
extends UFmap_Sru_Service_Get {

	protected $columns = array(
		'id'		=> 's.id',
		'name'		=> 's.name'
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'name'		=> self::TEXT
	);
	protected $tables = array(
		's' => 'services_type',
	);
	protected $pk = 's.id';
}

