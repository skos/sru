<?php
/**
 * wyciagniecie modeli urzadzen
 */
class UFmap_SruAdmin_DeviceModels_List
extends UFmap {

	protected $columns = array(
		'id'		=> 'm.id',
		'name'		=> 'm.name',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'name'		=> self::TEXT,
	);
	protected $tables = array(
		'm' => 'device_models',
	);
	protected $pk = 'id';
}
