<?php
/**
 * wyciagnięcie pojedynczej usługi, ale to raczej do usuniecia, bo niepotrzebne
 */
class UFmap_Sru_Service_Get
extends UFmap {

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