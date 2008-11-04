<?php
/**
 * dodanie kary
 */
class UFact_SruAdmin_Penalty_Add
extends UFact {

	const PREFIX = 'penaltyAdd';

	public function go() {
		try {
			$this->begin();
			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($this->_srv->get('req')->get->userId);

			$bean = UFra::factory('UFbean_SruAdmin_Penalty');
			$bean->fillFromPost(self::PREFIX, null, array('duration', 'reason', 'comment', 'computerId'));
			$bean->userId = $user->id;
			$bean->startAt = NOW;
			$bean->endAt = NOW + $bean->duration * 24 * 3600;
			$bean->createdAt = NOW;
			$bean->createdById = $this->_srv->get('session')->authAdmin; 
			$bean->amnestyAfter = NOW;
			
			if (null === $bean->computerId) {
				$bean->typeId = UFbean_SruAdmin_Penalty::TYPE_COMPUTERS;
			} elseif (0 === $bean->computerId) {
				$bean->typeId = UFbean_SruAdmin_Penalty::TYPE_WARNING;
				$bean->active = false;
			} else {
				$bean->typeId = UFbean_SruAdmin_Penalty::TYPE_COMPUTER;
			}
				
			$id = $bean->save();

			if (UFbean_SruAdmin_Penalty::TYPE_COMPUTERS === $bean->typeId) {
				$computers = UFra::factory('UFbean_Sru_ComputerList');
				try {
					$computers->listByUserId($user->id);

					foreach ($computers as $computer) {
						$penalty = UFra::factory('UFbean_SruAdmin_ComputerBan');
						$penalty->penaltyId = $id;
						$penalty->computerId = $computer['id'];
						$penalty->save(false);
					}
				} catch (UFex_Dao_NotFound $e) {
					// uzytkownik nie ma komputerow
				}
			} elseif (UFbean_SruAdmin_Penalty::TYPE_COMPUTER === $bean->typeId) {
				$computer = UFra::factory('UFbean_Sru_Computer');
				$computer->getByUserIdPK($user->id, $bean->computerId);

				$penalty = UFra::factory('UFbean_SruAdmin_ComputerBan');
				$penalty->penaltyId = $id;
				$penalty->computerId = $computer->id;
				$penalty->save(false);
			}

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			$this->commit();
		} catch (UFex_Dao_DataNotValid $e) {
			$this->rollback();
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			$this->rollback();
			UFra::error($e);
		}
	}
}
