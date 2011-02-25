<?
/**
 * dezaktywacja listy komputerów
 */
class UFact_SruApi_Computer_DeactivateNotSeen
extends UFact {
	
	const PREFIX = 'computersDelete';
	
	public function go(){
		try {
			$conf = UFra::shared('UFconf_Sru');

			$admin = UFra::factory('UFbean_SruAdmin_Admin');
			$admin->getFromHttp();

			$toDel = UFra::factory('UFbean_Sru_ComputerList');
			$toDel->deactivateNotSeen($conf->computersMaxNotSeen, $admin->id);
			$this->markOk(self::PREFIX);
		} catch (UFex $e) {
			$this->markErrors(self::PREFIX);
			UFra::error($e);
		}
	}
}
