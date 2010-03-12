<?php
/**
 * edycja usług
 */
class UFact_SruAdmin_Service_Edit
extends UFact {

	const PREFIX = 'serviceEdit';

	public function go() {
		try {
			$this->begin();
			$bean = UFra::factory('UFbean_SruAdmin_UserService');

			$post = $this->_srv->get('req')->post->{self::PREFIX};

			if (isset($post['activate'])) {
				$user = UFra::factory('UFbean_Sru_User');
				$user->getByPK($this->_srv->get('req')->get->userId);
				$servId =  key($post['activate']);
				$bean->state = false;
				$bean->servType = $servId;
				$bean->userId=$user->id;
			} else if (isset($post['deactivate'])) {
				$user = UFra::factory('UFbean_Sru_User');
				$user->getByPK($this->_srv->get('req')->get->userId);
				$userServId =  key($post['deactivate']);
				$bean->getByPK((int)$userServId);
				$bean->state = null;
			} else if (isset($post['activateFull'])) {
				$userServId =  key($post['activateFull']);
				$bean->getByPK((int)$userServId);
				$bean->state = true;
			} else if (isset($post['deactivateFull'])) {
				$userServId =  key($post['deactivateFull']);
				$bean->getByPK((int)$userServId);
				$bean->state = false;
			}
			
 			$bean->modifiedById = $this->_srv->get('session')->authAdmin;
 			$bean->save();
 			$this->markOk(self::PREFIX);
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
