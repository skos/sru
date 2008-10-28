<?
class UFacl_Core_Log {
	
	public function ufraShow() {
		$ip = UFra::services()->get('req')->server->REMOTE_ADDR;
		$conf = UFra::shared('UFconf_Core');
		return in_array($ip, $conf->debugAllowed);
	}
}
