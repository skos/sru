<?php
/**
 * dodanie wyjatkow fw komputera
 */
class UFact_Sru_Computer_FwExceptionsAdd
extends UFact {

	const PREFIX = 'computerFwExceptionsAdd';

	public function go() {
		try {
			$post = $this->_srv->get('req')->post->{self::PREFIX};	
			$this->begin();
			$computer = UFra::factory('UFbean_Sru_Computer');
			$computer->getByPK((int)$this->_srv->get('req')->get->computerId);
			/*
			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($computer->userId);

			$bean = UFra::factory('UFbean_Sru_FwApplication');

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
					$exception = UFra::factory('UFbean_SruAdmin_FwException');
					$exception->computerId = $computer->id;
					$exception->port = $exception;
					$exception->active = false;
					$exception->waiting = false;
					$exception->save();
				}
			}
*/
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
