<?php
/**
 * dodanie administratora
 */
class UFact_SruAdmin_Switch_Add
extends UFact {

	const PREFIX = 'switchAdd';

	public function go() {
		try {
			$this->begin();
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			
			$inventoryCard = UFra::factory('UFbean_SruAdmin_InventoryCard');
			$inventoryCard->fillFromPost(self::PREFIX, null, array('serialNo', 'inventoryNo', 'received'));
			$inventoryCard->dormitoryId = $post['invCardDormitory'];
			$inventoryCard->modifiedById = $this->_srv->get('session')->authAdmin;
			$inventoryCard->modifiedAt = NOW;
			$inventoryCardId = $inventoryCard->save();
			
			$bean = UFra::factory('UFbean_SruAdmin_Switch');
			$bean->fillFromPost(self::PREFIX, array('serialNo', 'inventoryNo', 'received','invCardDormitory'));


			if (!is_null($bean->hierarchyNo)) {
				$switch = UFra::factory('UFdao_SruAdmin_Switch');
				$exists = $switch->getByHierarchyNoDormLab($bean->hierarchyNo, $post['dormitory'], $bean->lab);

				if (!is_null($exists)) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Data hierarchyNo dupliacted in dormirory', 0, E_WARNING, array('hierarchyNo' => 'duplicated'));
				}
			} else {
				if (!is_null($bean->ip)) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'IP address without hierarchy no', 0, E_WARNING, array('ip' => 'noHierachyNo'));
				}
			}
			
			if (isset($post['ip']) && $post['ip'] != '') {
				try {
					$ip = UFra::factory('UFbean_Sru_Ipv4');
					$ip->getByIp($post['ip']);
				} catch (UFex_Dao_NotFound $e) {
					$this->markErrors(self::PREFIX, array('ip'=>'notFound'));
					return;
				} catch (UFex_Db_QueryFailed $e) {
					$this->markErrors(self::PREFIX, array('ip'=>''));
					return;
				}
			}
			
			$bean->inventoryCardId = $inventoryCardId;
			$bean->modifiedById = $this->_srv->get('session')->authAdmin;
			$bean->modifiedAt = NOW;
			$id = $bean->save();

			$model = UFra::factory('UFbean_SruAdmin_SwitchModel');
			$model->getByPK($bean->modelId);
			for ($i = 1; $i < $model->ports + 1; $i++) {
				$port = UFra::factory('UFbean_SruAdmin_SwitchPort');
				$port->switchId = $id;
				$port->ordinalNo = $i;
				$port->save();
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
