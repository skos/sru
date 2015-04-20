<?php
/**
 * edycja wniosku 
 */
class UFact_Sru_FwExceptionApplication_Edit
extends UFact {

	const PREFIX = 'fwExceptionApplicationEdit';

	public function go() {
		try {
			$conf = UFra::shared('UFconf_Sru');
			
			$post = $this->_srv->get('req')->post->{self::PREFIX};	
			$this->begin();

			$bean = UFra::factory('UFbean_Sru_FwExceptionApplication');
			$bean->getByPK((int)$this->_srv->get('req')->get->appId);
			$bean->fillFromPost(self::PREFIX, null, array('sspgComment', 'sspgOpinion'));
			if (!array_key_exists('sspgOpinion', $post)) {
				$this->markErrors(self::PREFIX, array('sspgOpinion'=>'empty'));
				return;
			}
			$bean->sspgOpinionBy = $this->_srv->get('session')->auth;
			$bean->sspgOpinionAt = NOW;
			$bean->save();
			
			$fwExceptions = UFra::factory('UFbean_SruAdmin_FwExceptionList');
			$fwExceptions->listByApplictionId($bean->id);
			foreach ($fwExceptions as $exc) {
				$fwException = UFra::factory('UFbean_SruAdmin_FwException');
				$fwException->getByPK($exc['id']);
				$fwException->waiting = false;
				$fwException->active = $post['sspgOpinion'];
				$fwException->save();
			}
			
			// wyslanie maila do usera
			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($bean->userId);
			$box = UFra::factory('UFbox_Sru');
			$sender = UFra::factory('UFlib_Sender');
			if ($bean->sspgOpinion == false) {
				$title = $box->rejectedFwExceptionApplicationMailTitle($bean, $user);
				$body = $box->rejectedFwExceptionApplicationMailBody($bean, $user);
				
			} else {
				$title = $box->approvedFwExceptionApplicationMailTitle($bean, $user);
				$body = $box->approvedFwExceptionApplicationMailBody($bean, $user);
			}
			$sender->send($user, $title, $body, self::PREFIX);
			
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
