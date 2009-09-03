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
				$userServId =  key($post['activate']);
				$bean->getByPK((int)$userServId);
				$bean->state = true;
			} else {
				$userServId =  key($post['deactivate']);
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
