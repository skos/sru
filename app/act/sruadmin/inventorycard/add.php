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
			$id = $bean->save();
			
			$comp = UFra::factory('UFbean_Sru_Computer');
			$comp->getByPK((int)$this->_srv->get('req')->get->computerId);
			$comp->inventoryCardId = $id;
			$comp->save();

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
