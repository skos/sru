<?php
/**
 * edycja pokoju
 */
class UFact_SruAdmin_Room_Edit
extends UFact {

	const PREFIX = 'roomEdit';

	public function go() {
		try {
			$this->begin();
			$bean = UFra::factory('UFbean_SruAdmin_Room');
			$bean->getByAlias($this->_srv->get('req')->get->dormAlias, $this->_srv->get('req')->get->roomAlias);
			
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			
			
			$bean->fillFromPost(self::PREFIX, array(), array('comment'));
														
			$bean->save();

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			$this->commit();
		} catch (UFex_Dao_DataNotValid $e) {
			$this->rollback();
		}
	}
}
