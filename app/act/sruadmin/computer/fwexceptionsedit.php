<?php
/**
 * edycja wyjatkow fw komputera
 */
class UFact_SruAdmin_Computer_FwExceptionsEdit
extends UFact {

	const PREFIX = 'computerFwExceptionsEdit';

	public function go() {
		try {
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$this->begin();
			$computer = UFra::factory('UFbean_Sru_Computer');
			$computer->getByPK((int)$this->_srv->get('req')->get->computerId);
			
			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($computer->userId);

			$bean = UFra::factory('UFbean_SruAdmin_FwException');
			$deleted = array();
			if (isset($post['exceptions'])) {
				while (key($post['exceptions'])) {
					if (current($post['exceptions'])) {
						$exceptionId = key($post['exceptions']);
						$bean->getByPK($exceptionId);
						array_push($deleted, $bean->port);
						$bean->del();
					}
					next($post['exceptions']);
				}
			}

			$added = array();
			if (array_key_exists('newExceptions', $post) && $post['newExceptions'] != '') {
				if (!UFbean_SruAdmin_FwException::validateExceptionsStringFormat($post['newExceptions'])) {
					$this->markErrors(self::PREFIX, array('port'=>'regexp'));
					return;
				}
				$newExceptions = explode(',', $post['newExceptions']);
				if (in_array('0', $newExceptions)) {
					try {
						$exceptionsList = UFra::factory('UFbean_SruAdmin_FwExceptionList');
						$exceptionsList->listActiveByComputerId($computer->id);
						foreach ($exceptionsList as $exception) {
							$bean->getByPK($exception['id']);
							array_push($deleted, $bean->port);
							$bean->del();
						}
					} catch (UFex_Dao_NotFound $e) {
						// brak inny wyjatkow
					}
					$bean = UFra::factory('UFbean_SruAdmin_FwException');
					$bean->computerId = $computer->id;
					$bean->port = 0;
					$bean->active = true;
					$bean->waiting = false;
					$bean->modifiedBy = $this->_srv->get('session')->authAdmin;
					array_push($added, 0);
					$bean->save();
				} else {
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
						$bean = UFra::factory('UFbean_SruAdmin_FwException');
						$bean->computerId = $computer->id;
						$bean->port = $exception;
						$bean->active = true;
						$bean->waiting = false;
						$bean->modifiedBy = $this->_srv->get('session')->authAdmin;
						array_push($added, $exception);
						$bean->save();
					}
				}
			}

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			$this->commit();

			$conf = UFra::shared('UFconf_Sru');
			if ($conf->sendEmail && (count($deleted) > 0 || !is_null($added))) {
				// wyslanie maila do admina
				$admin = UFra::factory('UFbean_SruAdmin_Admin');
				$admin->getByPK($this->_srv->get('session')->authAdmin);

				$box = UFra::factory('UFbox_SruAdmin');
				$sender = UFra::factory('UFlib_Sender');
				$title = $box->hostFwExceptionsChangedMailTitle($computer);
				$body = $box->hostFwExceptionsChangedMailBody($computer, $deleted, $added, $admin);
				$sender->sendMail("adnet@ds.pg.gda.pl", $title, $body, self::PREFIX);
				
				// wyslanie maila do usera
				if ($user->typeId != UFbean_Sru_User::TYPE_SKOS) {
					$box = UFra::factory('UFbox_Sru');
					$sender = UFra::factory('UFlib_Sender');
					$title = $box->hostFwExceptionsChangedMailTitle($user, $computer);
					$body = $box->hostFwExceptionsChangedMailBody($user, $computer, $deleted, $added);
					$sender->send($user, $title, $body, self::PREFIX);
				}
			}

		} catch (UFex_Dao_DataNotValid $e) {
			$this->rollback();
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			$this->rollback();
			UFra::error($e);
		}
	}
}
