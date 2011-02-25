<?
/**
 * dezaktywacja administratora
 */
class UFact_SruApi_Admin_Deactivate
extends UFact {
	
	const PREFIX = 'adminsDelete';
	
	public function go(){
		try{
			$toDel = UFra::factory('UFbean_SruAdmin_AdminList');
			$toDel->deactivateOutdated();
			$this->markOk(self::PREFIX);
		} catch (UFex $e) {
			$this->markErrors(self::PREFIX);
			UFra::error($e);
		}
	}
}
