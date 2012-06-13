<?php

class UFmap_SruAdmin_SwitchPort_GetPort
extends UFmap {
	protected $columns = array(
		'penaltyId' => 'b.id',
		'portId' => 'p.id',
		'userId' => 'u.id',
		'switchId' => 'p.switch',
		'ordinalNo' => 'p.ordinal_no',
		'locationAlias' => 'p.location'
	);
	
	protected $columnTypes = array(
		'penaltyId' => self::INT,
		'portId' => self::INT,
		'userId' => self::INT,
		'switchId' => self::INT,
		'ordinalNo' => self::INT,
		'locationAlias' => self::NULL_TEXT
	);
	
	protected $tables = array(
		'b' => 'penalties',
	);
	
	protected $joins = array( 
		'p' => 'switches_port',
		'u' => 'users'
	);
	
	protected $joinOns = array(
		'p' => 'b.id=p.penalty_id',
		'u' => 'u.id=b.user_id'
	);
	//protected $pk = 'b.id';
}