<?
/**
 * wyjatek fw komputera
 */
class UFbean_SruAdmin_FwException
extends UFbeanSingle {	
	protected function validatePort($val, $change) {
		try {
			$computer = UFra::factory('UFbean_Sru_Computer');
			$computer->getByPK((int)$this->_srv->get('req')->get->computerId);
			
			$bean = UFra::factory('UFbean_SruAdmin_FwException');
			$bean->getActive($val, $computer->id);
			
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
		}
	}
	
	public static function validateExceptionsStringFormat($fwExceptions) {
		$exceptions = explode(',', $fwExceptions);
		
		foreach ($exceptions as $exception) {
			$exception = trim($exception);
			if (!ctype_digit($exception)) {
				return false;
			}
		}
		return true;
	}
}
