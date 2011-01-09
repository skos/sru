<?php
/**
 * dodanie szablonu kary
 */
class UFact_SruAdmin_PenaltyTemplate_Add
extends UFact {

	const PREFIX = 'penaltyTemplateAdd';

	public function go() {
		try {
			$this->begin();

			$bean = UFra::factory('UFbean_SruAdmin_PenaltyTemplate');

			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$bean->fillFromPost(self::PREFIX, null);
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
