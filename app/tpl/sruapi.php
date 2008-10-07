<?
/**
 * szablon api sru
 */
class UFtpl_SruApi
extends UFtpl_Common {

	public function configDhcp(array $d) {
		$d['computers']->write('configDhcp');
	}
}
