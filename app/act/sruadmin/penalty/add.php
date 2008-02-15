<?php
/**
 * dodanie kary
 */
class UFact_SruAdmin_Penalty_Add
extends UFact {

	const PREFIX = 'penaltyAdd';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_Penalty');
			$bean->fillFromPost(self::PREFIX);
			
			if($bean->endAt <=  $bean->startAt)
			{
				$this->markErrors(self::PREFIX, array('endAt'=>'noSense'));
				return;
			}
					
			$bean->adminId = $this->_srv->get('session')->authAdmin; 
			$bean->userId  = $this->_srv->get('req')->get->userId; //@todo: a jak to zvalidowac?
			
			$bean->modifiedBy = null;
			$bean->modifiedAt = NOW;
			$bean->createdAt = NOW;
			
			$bean->amnestyAfter = NOW;
			
				
			$id = $bean->save();


			$this->postDel(self::PREFIX);//@todo:to nie powinno kasowac danych z posta, zeby odswiezaniem nie dodawac kar?
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		}
	}
}
