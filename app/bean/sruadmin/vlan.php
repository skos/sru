<?
/**
 * Vlan
 */
class UFbean_SruAdmin_Vlan
extends UFbeanSingle {
	
	const DEFAULT_VLAN = 42;
	
	const VOIP_PG = 38;
	const DS_ADM = 40;
	const DS_ORGAN = 41;
	const DS_PG = 42;
	const HAJ = 47;
	const DS_SRW = 1042;
	const DS_SW = 1043;
	
	protected function chooseTemplate() {
		return;
	}
}
