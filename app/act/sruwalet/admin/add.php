<?php
/**
 * dodanie administratora Waleta
 */
class UFact_SruWalet_Admin_Add
extends UFact {

	const PREFIX = 'adminAdd';

	public function go() {
		try {
			$this->begin();
			$bean = UFra::factory('UFbean_SruWalet_Admin');
			$bean->fillFromPost(self::PREFIX, array('dorm'));
			$bean->modifiedById = $this->_srv->get('session')->authWaletAdmin;
			$id = $bean->save();

			$bean->getByPK($id);
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			while (!is_null(key($post['dorm']))) {
				if (current($post['dorm'])) {
					$admDorm = UFra::factory('UFbean_SruWalet_AdminDormitory');
					$admDorm->admin = $bean->id;
					$admDorm->dormitory = key($post['dorm']);
					$admDorm->save();
				}
				next($post['dorm']);
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
