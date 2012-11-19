<?
/**
 * dezaktywacja administratora
 */
class UFact_SruApi_Admin_Deactivate
extends UFact {
	
	const PREFIX = 'adminsDelete';
	
	public function go(){
		try{
			$admin = UFra::factory('UFbean_SruAdmin_Admin');
			$admin->getFromHttp();

			$toDel = UFra::factory('UFbean_SruAdmin_AdminList');
			$toDel->deactivateOutdated($admin->id);
			$this->markOk(self::PREFIX);
		} catch (UFex $e) {
			$this->markErrors(self::PREFIX);
			UFra::error($e);
		}
	}
}
