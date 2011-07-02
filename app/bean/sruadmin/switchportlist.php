<?php
/**
 * porty switcha
 */
class UFbean_SruAdmin_SwitchPortList
extends UFbeanList {
	public function updatePenaltyIdByPortId($portId, $penaltyId = null) {
		return $this->dao->updatePenaltyIdByPortId($portId, $penaltyId);
	}
}
