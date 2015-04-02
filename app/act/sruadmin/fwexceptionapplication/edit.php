<?php
/**
 * edycja wniosku 
 */
class UFact_SruAdmin_FwExceptionApplication_Edit
extends UFact {

	const PREFIX = 'fwExceptionApplicationEdit';

	public function go() {
		try {
			$conf = UFra::shared('UFconf_Sru');
			
			$post = $this->_srv->get('req')->post->{self::PREFIX};	
			$this->begin();

			$bean = UFra::factory('UFbean_Sru_FwExceptionApplication');
			$bean->getByPK((int)$this->_srv->get('req')->get->appId);
			$bean->fillFromPost(self::PREFIX, null, array('skosComment', 'skosOpinion'));
			if (!array_key_exists('skosOpinion', $post)) {
				$this->markErrors(self::PREFIX, array('skosOpinion'=>'empty'));
				return;
			}
			$bean->skosOpinionBy = $this->_srv->get('session')->authAdmin;
			$bean->skosOpinionAt = NOW;
			$bean->save();
			
			$fwExceptions = UFra::factory('UFbean_SruAdmin_FwExceptionList');
			$fwExceptions->listByApplictionId($bean->id);
			foreach ($fwExceptions as $exc) {
				$fwException = UFra::factory('UFbean_SruAdmin_FwException');
				$fwException->getByPK($exc['id']);
				$fwException->waiting = false;
				$fwException->active = $post['skosOpinion'];
				$fwException->save();
			}
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
