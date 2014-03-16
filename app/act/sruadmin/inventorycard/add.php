<?php
/**
 * dodanie karty wyposazenia
 */
class UFact_SruAdmin_InventoryCard_Add
extends UFact {

	const PREFIX = 'inventoryCardAdd';

	public function go() {
		try {
			$this->begin();
			
			$bean = UFra::factory('UFbean_SruAdmin_InventoryCard');
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
