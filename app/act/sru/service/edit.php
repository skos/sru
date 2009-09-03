<?php
/**
 * edycja usług
 */
class UFact_Sru_Service_Edit
extends UFact {

	const PREFIX = 'serviceEdit';

	public function go() {
		try {
			$this->begin();

			$bean = UFra::factory('UFbean_Sru_UserService');

			$user = UFra::factory('UFbean_Sru_User');
			$user->getFromSession();

			$post = $this->_srv->get('req')->post->{self::PREFIX};
			
			if (isset($post['activate'])) {
				$servId =  key($post['activate']);
				$bean->state = false;
				$bean->servType = $servId;
				$bean->userId=$user->id;
				$bean->save();
				$this->markOk(self::PREFIX);
			} else {
				$userServId =  key($post['deactivate']);
				$bean->getByPK((int)$userServId);
				if ($bean->userId == $user->id) {
					$bean->state = null;
					$bean->modifiedById = null;
					$bean->save();
					$this->markOk(self::PREFIX);
				}
			}					
			
			$this->postDel(self::PREFIX);
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
