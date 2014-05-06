<?php
/**
 * edycja urzadzenia
 */
class UFact_SruAdmin_Device_Edit
extends UFact {

	const PREFIX = 'deviceEdit';

	public function go() {
		try {
			$this->begin();
			$bean = UFra::factory('UFbean_SruAdmin_Device');
			$bean->getByPk($this->_srv->get('req')->get->deviceId);
			$bean->fillFromPost(self::PREFIX);
		
			$bean->modifiedById = $this->_srv->get('session')->authAdmin;
			$bean->modifiedAt = NOW;
			$bean->save();

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
