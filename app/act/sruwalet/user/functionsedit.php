<?php
/**
 * edycja funkcji usera
 */
class UFact_SruWalet_User_FunctionsEdit
extends UFact {

	const PREFIX = 'userFunctionsEdit';

	public function go() {
		try {
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$this->begin();
			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK((int)$this->_srv->get('req')->get->userId);

			$bean = UFra::factory('UFbean_Sru_UserFunction');
			if (isset($post['functions'])) {
				while (key($post['functions'])) {
					if (current($post['functions'])) {
						$functionId = key($post['functions']);
						$bean->getByPK($functionId);
						$bean->del();
					}
					next($post['functions']);
				}
			}

			if (array_key_exists('newFunction', $post) && $post['newFunction'] != '') {
				try {
					$functions = UFra::factory('UFbean_Sru_UserFunctionList');
					$functions->listForUserId($user->id);
					foreach ($functions as $func) {
						if ($func['functionId'] == $post['newFunction']) {
							$this->markErrors(self::PREFIX, array('newFunction'=>'duplicated'));
							return;
						}
					}
				} catch (UFex_Dao_NotFound $e) {
				}
				$bean = UFra::factory('UFbean_Sru_UserFunction');
				$bean->userId = $user->id;
				$bean->functionId = $post['newFunction'];
				if ($post['newFunctionComment'] != '') {
					$bean->comment = $post['newFunctionComment'];
				}
				if ($bean->functionId != UFbean_Sru_UserFunction::TYPE_CAMPUS_CHAIRMAN) {
					$bean->dormitoryId = $user->dormitoryId;
				}
				$bean->save();
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
