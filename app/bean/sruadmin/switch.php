<?php
/**
 * switch
 */
class UFbean_SruAdmin_Switch
extends UFbeanSingle {
    
	public $left;
	public $right;

	protected function validateSerialNo($val, $change) {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_Switch');
			$bean->getBySerialNo($val);
			if ($change && $this->data['id'] == $bean->id) {
				return;
			}
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
		}
	}

	protected function validateInventoryNo($val, $change) {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_Switch');
			$bean->getByInventoryNo($val);
			if ($change && $this->data['id'] == $bean->id) {
				return;
			}
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
		}
	}

	protected function validateIp($val, $change) {
		try {
			if ($val == '') return;
			$bean = UFra::factory('UFbean_SruAdmin_Switch');
			$bean->getByIp($val);
			if ($change && $this->data['id'] == $bean->id) {
				return;
			}
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
		}
	}
        
	public function leftRight(){
		$dorms = UFra::factory('UFbean_SruAdmin_SwitchList');
		$dorms->listAll();
		$left = null;
		$middle = null;
		$right = null;
		if($dorms->valid()){
			$left = $dorms->current();
		}
		if($left['id'] == $this->id){//brak lewego
			$left = null;
			$dorms->next();
			if($dorms->valid()){
				$right = $dorms->current();
			}
		}else{
			$dorms->next();
			if($dorms->valid()){
				$middle = $dorms->current();
			}
			while(true){
				$right = null;
				$dorms->next();
				if($dorms->valid()){
					$right = $dorms->current();
				}
				if($this->id == $middle['id']){
					break;
				}
				$left = $middle;
				$middle = $right;
			}
		}
		$this->left = $left;
		$this->right = $right;
	}
        
}
