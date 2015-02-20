<?
/**
 * calkowite usuniecie usera
 */
class UFact_SruApi_User_Remove
extends UFact {

	const PREFIX = 'userRemove';

	public function go() {
		try {
			$this->begin();
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->getByPK((int)$this->_srv->get('req')->get->userId);
			
			try {
				$computers = UFra::factory('UFbean_Sru_ComputerList');
				$computers->listByUserIdInactive($bean->id);
				foreach ($computers as $computer) {
					$comp = UFra::factory('UFbean_Sru_Computer');
					$comp->getByPK($computer['id']);
					$comp->del();
				}
			} catch (UFex_Dao_NotFound $e) {
			}
			
			try {
				$penalties = UFra::factory('UFbean_SruAdmin_PenaltyList');
				$penalties->listAllByUserId($bean->id);
				foreach ($penalties as $penalty) {
					$pen = UFra::factory('UFbean_SruAdmin_Penalty');
					$pen->getByPK($penalty['id']);
					$pen->del();
				}
			} catch (UFex_Dao_NotFound $e) {
			}
			
			$bean->del();

			$this->commit();
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_NotFound $e) {
			$this->rollback();
		} catch (UFex $e) {
			$this->rollback();
			$this->markErrors(self::PREFIX);
			UFra::error($e);
		}
	}
}
