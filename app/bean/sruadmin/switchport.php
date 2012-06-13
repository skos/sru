<?php
/**
 * port switcha
 */
class UFbean_SruAdmin_SwitchPort
extends UFbeanSingle {
	
	public function erasePenalty($penaltyId){
		return $this->dao->erasePenalty($penaltyId);
	}
	
	public function getByPenaltyUserId($penaltyId, $userId){
		return $this->dao->getByPenaltyUserId($penaltyId, $userId);
	}
}
