<?
/**
 * Vlan
 */
class UFbean_SruAdmin_Vlan
extends UFbeanSingle {	
	
	protected function chooseTemplate() {
		return;
	}
	
	public static function getDefaultVlan() {
		return UFra::shared('UFconf_Sru')->defaultVlan;
	}
}
