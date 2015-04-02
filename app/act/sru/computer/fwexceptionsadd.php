<?php
/**
 * dodanie wyjatkow fw komputera
 */
class UFact_Sru_Computer_FwExceptionsAdd
extends UFact {

	const PREFIX = 'computerFwExceptionsAdd';

	public function go() {
		try {
			$conf = UFra::shared('UFconf_Sru');
			
			$post = $this->_srv->get('req')->post->{self::PREFIX};	
			$this->begin();
			$computer = UFra::factory('UFbean_Sru_Computer');
			$computer->getByPK((int)$this->_srv->get('req')->get->computerId);
			
			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($computer->userId);

			$bean = UFra::factory('UFbean_Sru_FwExceptionApplication');
			$bean->fillFromPost(self::PREFIX, null, array('comment', 'host', 'validTo', 'purpose', 'newExceptions'));
			if (!array_key_exists('purpose', $post)) {
				$this->markErrors(self::PREFIX, array('purpose'=>'empty'));
				return;
			}
			if ($post['validTo'] > $conf->usersAvailableTo) {
				$this->markErrors(self::PREFIX, array('validTo'=>'tooLong'));
				return;
			}
			$bean->userId = $user->id;
			if ($post['purpose'] == UFbean_Sru_FwExceptionApplication::TYPE_UNIVERSITY) {
				$bean->universityEducation = true;
				$bean->selfEducation = false;
			} else {
				$bean->universityEducation = false;
				$bean->selfEducation = true;
			}
			
			$appId = $bean->save();
			$bean->getByPK($appId);

			if (array_key_exists('newExceptions', $post) && $post['newExceptions'] != '') {
				if (!UFbean_SruAdmin_FwException::validateExceptionsStringFormat($post['newExceptions'])) {
					$this->markErrors(self::PREFIX, array('port'=>'regexp'));
					return;
				}
				$newExceptions = explode(',', $post['newExceptions']);
				try {
					$exceptionsList = UFra::factory('UFbean_SruAdmin_FwExceptionList');
					$exceptionsList->listActiveByComputerId($computer->id);
					foreach ($exceptionsList as $exception) {
						if ($exception['port'] == 0) {
							$this->markErrors(self::PREFIX, array('port'=>'regexp'));
							return;
						}
					}
				} catch (UFex_Dao_NotFound $e) {
					// brak inny wyjatkow
				}
				foreach ($newExceptions as $exception) {
					$exception = trim($exception);
					if ($exception == 0) {
						$this->markErrors(self::PREFIX, array('port'=>'regexp'));
						return;
					}
					$exc = UFra::factory('UFbean_SruAdmin_FwException');
					$exc->applicationId = $appId;
					$exc->computerId = $computer->id;
					$exc->port = $exception;
					$exc->active = false;
					$exc->waiting = true;
					$exc->save();
				}
			}
			
			if ($conf->sendEmail) {
				// wyslanie maila do Przewodncizacych OS
				$box = UFra::factory('UFbox_Sru');
				$sender = UFra::factory('UFlib_Sender');
				$title = $box->newFwExceptionApplicationMailTitle();
				$body = $box->newFwExceptionApplicationMailBody($bean);
				
				try {
					$chairmans = UFra::factory('UFbean_Sru_UserFunctionList');
					$chairmans->listByFunctionId(UFbean_Sru_UserFunction::TYPE_CAMPUS_CHAIRMAN);
					
					foreach ($chairmans as $chairman) {
						$sender->sendMail($chairman['userEmail'], $title, $body);
					}
				} catch (UFex_Dao_NotFound $e) {
				}
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
