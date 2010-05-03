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
			$bean = UFra::factory('UFbean_SruAdmin_Switch');
			$bean->fillFromPost(self::PREFIX);

			if (!is_null($bean->hierarchyNo)) {
				$switch = UFra::factory('UFdao_SruAdmin_Switch');
				$exists = $switch->getByHierarchyNoAndDorm($bean->hierarchyNo, $bean->dormitoryId);

				if (!is_null($exists)) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Data hierarchyNo dupliacted in dormirory', 0, E_WARNING, array('hierarchyNo' => 'duplicated'));
				}
			}
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
