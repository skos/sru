<?php
/**
 * wyciagniecie pojedynczego modelu switcha
 */
class UFmap_SruAdmin_SwitchModel_Get
extends UFmap {

	protected $columns = array(
		'id'		=> 'm.id',
		'model'		=> 'm.model_name',
		'modelNo'	=> 'm.model_no',
		'ports'		=> 'm.ports_no',
	);
	protected $columnTypes = array(
		'id'		=> self::INT,
		'model'		=> self::TEXT,
		'modelNo'	=> self::TEXT,
		'ports'		=> self::INT,
	);
	protected $tables = array(
		'm' => 'switches_model',
	);
	protected $pk = 'id';
}
