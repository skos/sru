<?
/**
 * edycja kary
 */
class UFact_SruAdmin_Penalty_Edit
extends UFact {

	const PREFIX = 'penaltyEdit';

	public function go() {
		try {
			$this->begin();

			$bean = UFra::factory('UFbean_SruAdmin_Penalty');
			$bean->getByPK($this->_srv->get('req')->get->penaltyId);
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			if (!$bean->active) {
				UFra::error('Penalty '.$bean->id.' is not active');
				return;
			}
			$acl = $this->_srv->get('acl');
			$admin = UFra::factory('UFbean_SruAdmin_Admin');
			$admin->getFromSession();
			if(!$acl->sruAdmin('penalty', 'editOne', $bean->id)) {
				UFra::error('Admin '.$d['admin']->id.' dont have permission to edit this penalty');
				return;
			}

			if ('' === $post['endAt']) {
				$bean->endAt = NOW;
			} else {
				$bean->fillFromPost(self::PREFIX, null, array('endAt'));
			}
			$bean->modifiedAt = NOW;
			$bean->modifiedById = $this->_srv->get('session')->authAdmin; 
			if ($bean->endAt <= NOW) {
				$bean->endAt = NOW;
				$bean->amnestyById = $bean->modifiedById;
				$bean->amnestyAt = NOW;
				$bean->active = false;
			}
			
			$bean->save();

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
