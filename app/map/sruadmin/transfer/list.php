<?php
/**
 * wyciagniecie statsów transferu
 */
class UFmap_SruAdmin_Transfer_List
extends UFmap_SruAdmin_Transfer_Get {
	protected $columns = array(
		'ip'		=> 'l.ip',
		'hostId'	=> 'c.id',
		'host'		=> 'c.host',
		'isAdmin'	=> 'c.can_admin',
		'typeId'	=> 'c.type_id',
		'isBanned'	=> 'c.banned',
		'bytes_sum' 	=> 'sum(l.bytes)/1024/1800',
		'bytes_min' 	=> 'min(l.bytes)/1024/60',
		'bytes_max' 	=> 'max(l.bytes)/1024/60',
	);
}

