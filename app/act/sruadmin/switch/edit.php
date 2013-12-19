<?php
/**
 * edycja administratora
 */
class UFact_SruAdmin_Switch_Edit
extends UFact {

	const PREFIX = 'switchEdit';

	public function go() {
		try {
			$this->begin();
			$bean = UFra::factory('UFbean_SruAdmin_Switch');
			$bean->getBySerialNo($this->_srv->get('req')->get->switchSn);
			$modelId = $bean->modelId;

			try {
				$bean->fillFromPost(self::PREFIX);
			} catch (UFex_Db_QueryFailed $e) {
				$this->rollback();
				$this->markErrors(self::PREFIX, array('ip'=>'regexp'));
				return;
			}
			$post = $this->_srv->get('req')->post->{self::PREFIX};

			if (!is_null($bean->hierarchyNo)) {
				$switch = UFra::factory('UFdao_SruAdmin_Switch');
				$exists = $switch->getByHierarchyNoDormLab($bean->hierarchyNo, $post['dormitory'], $bean->lab);
				if (!is_null($exists) && $exists['id'] != $bean->id) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Data hierarchyNo dupliacted in dormitory', 0, E_WARNING, array('hierarchyNo' => 'duplicated'));
				}
			} else {
				if (!is_null($bean->ip)) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'IP address without hierarchy no', 0, E_WARNING, array('ip' => 'noHierachyNo'));
				}
			}
			if ($modelId != $bean->modelId && (!array_key_exists('ignoreModelChange', $post) || 0 == $post['ignoreModelChange'])) {
				throw UFra::factory('UFex_Dao_DataNotValid', 'Change of switch model', 0, E_WARNING,  array('model' => 'change'));
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
			
			$bean->save();

			if ($modelId != $bean->modelId) {
				try {
					$portlist = UFra::factory('UFbean_SruAdmin_SwitchPortList');
					$portlist->listBySwitchId($bean->id);
					foreach ($portlist as $swport) {
						$port = UFra::factory('UFbean_SruAdmin_SwitchPort');
						$port->getByPK($swport['id']);
						$port->del();
					}
				} catch (UFex_Dao_NotFound $ex) {
				}
				$model = UFra::factory('UFbean_SruAdmin_SwitchModel');
				$model->getByPK($bean->modelId);
				for ($i = 1; $i < $model->ports + 1; $i++) {
					$port = UFra::factory('UFbean_SruAdmin_SwitchPort');
					$port->switchId = $bean->id;
					$port->ordinalNo = $i;
					$port->save();
				}
			}

			$this->_srv->get('req')->get->newSwitchSn = $bean->serialNo; // jeśli się zmienił, to musimy się odwołać po nowym
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
