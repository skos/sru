<?php
/**
 * szablon kary
 */
class UFbean_SruAdmin_PenaltyTemplate
extends UFbeanSingle {

	protected function validateTitle($val, $change) {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_PenaltyTemplate');
			$bean->getByTitle($val);
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
		}
	}
}
