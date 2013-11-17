<?php

/**
 * Obsługa Zabbiksa
 */
class UFlib_Zabbix {

	public function getAllActiveProblems() {
		require_once 'Zabbix/ZabbixApiAbstract.class.php';
		require_once 'Zabbix/ZabbixApi.class.php';

		$conf = UFra::shared('UFconf_Sru');
		$url = $conf->zabbixUrl . 'api_jsonrpc.php';
		$username = $conf->zabbixUser;
		$password = $conf->zabbixPass;

		try {
			$api = new ZabbixApi($url, $username, $password);
			
			$triggers = $api->triggerGet(array(
			    'output' => 'extend',
			    'monitored' => 1,
			    'withUnacknowledgedEvents' => 1,
			    'skipDependent' => 1,
			    'expandData' => 1,
			    'expandDescription' => 1,
			    'active' => TRUE,
			    'only_true' => TRUE,
			    'sortfield' => 'priority',
			    'sortorder' => 'DESC',
			    'filter' => array('value' => 1),
			));

			return $triggers;
		} catch (Exception $e) {
			return null;
		}
	}
}