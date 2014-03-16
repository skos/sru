<?php
/**
 * edycja karty wyposazenia
 */
class UFact_SruAdmin_InventoryCard_Edit
extends UFact {

	const PREFIX = 'inventoryCardEdit';

	public function go() {
		try {
			$this->begin();
			$post = $this->_srv->get('req')->post->{self::PREFIX};

			$bean = UFra::factory('UFbean_SruAdmin_InventoryCard');
			$bean->getByPk($post['inventoryCardId']);
			$bean->fillFromPost(self::PREFIX, array('inventoryCardId'));
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
