<?php
/**
 * dodanie administratora
 */
class UFact_SruAdmin_Admin_Add
extends UFact {

	const PREFIX = 'adminAdd';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_Admin');
			$bean->fillFromPost(self::PREFIX);
			if ($bean->typeId == UFacl_SruAdmin_Admin::BOT) {
				$bean->passwordInner = $bean->password;
			}
			$bean->modifiedById = $this->_srv->get('session')->authAdmin;
			$id = $bean->save();

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
