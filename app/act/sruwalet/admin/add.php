<?php
/**
 * dodanie administratora Waleta
 */
class UFact_SruWalet_Admin_Add
extends UFact {

	const PREFIX = 'adminAdd';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_SruWalet_Admin');
			$bean->fillFromPost(self::PREFIX);
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
