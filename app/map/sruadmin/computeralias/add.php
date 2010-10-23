<?
/**
 * dodanie aliasu komputera
 */
class UFmap_SruAdmin_ComputerAlias_Add
extends UFmap_SruAdmin_ComputerAlias_Get {
	protected $valids = array(
		'host' => array('textMin'=>1, 'textMax'=>50, 'regexp'=>'^[a-z][-a-z0-9]*$|^[a-z][-a-z0-9.]*[-a-z0-9]+$'),
	);
}
