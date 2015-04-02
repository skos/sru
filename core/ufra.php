<?php

class UFraCore {
	
	/**
	 * uslugi 
	 */
	static protected $services;

	/**
	 * zebrane logi z frameworka
	 */
	static protected $logs = array();

	/**
	 * obiekty wspoldzielone 
	 */
	static protected $sharedClasses = array();

	/**
	 * klasy ladowane z core'a ufry
	 */
	static protected $classMap = array(
		// klasy podstawowe
		'ufacl'        => 'acl/acl',
		'ufact'        => 'act/act',
		'ufbeanlist'   => 'bean/beanlist',
		'ufbeansingle' => 'bean/beansingle',
		'ufbean'       => 'bean/bean',
		'ufbox'        => 'box/box',
		'ufconf'       => 'conf/conf',
		'ufctl'        => 'ctl/ctl',
		'ufdao'        => 'dao/dao',
		'ufdaobase'    => 'dao/daobase',
		'ufdaoredis'   => 'dao/daoredis',
		'ufex'         => 'ex/ex',
		'ufmap'        => 'map/map',
		'ufmapredis'   => 'map/mapredis',
		'uftpl'        => 'tpl/tpl',
		'ufview'       => 'view/view',

		// klasy pochodne
		'ufbean_core_loglist'        => 'bean/core/loglist',
		'ufbox_log'                  => 'box/log',
		'ufdao_core_log'             => 'dao/core/log',
		'ufex_core_datanotfound'     => 'ex/core/datanotfound',
		'ufex_core_nomethod'         => 'ex/core/nomethod',
		'ufex_core_noparameter'      => 'ex/core/noparameter',
		'ufex_core_servicenotfound'  => 'ex/core/servicenotfound',
		'ufex_core_viewchange'       => 'ex/core/viewchange',
		'ufex_dao_datanotvalid'      => 'ex/dao/datanotvalid',
		'ufex_dao_notfound'          => 'ex/dao/notfound',
		'ufex_db_badqueryparam'      => 'ex/db/badqueryparam',
		'ufex_db_noquerytables'      => 'ex/db/noquerytables',
		'ufex_db_noqueryvalues'      => 'ex/db/noqueryvalues',
		'ufex_db_notconnected'       => 'ex/db/notconnected',
		'ufex_db_queryfailed'        => 'ex/db/queryfailed',
		'ufex_lib_badresult'         => 'ex/lib/badresult',
		'ufex_tpl_directorynotfound' => 'ex/tpl/directorynotfound',
		'ufex_tpl_notfound'          => 'ex/tpl/notfound',
		'ufex_tpl_notset'            => 'ex/tpl/notset',
		'uflib_cachexcache'          => 'lib/cachexcache',
		'uflib_cache'                => 'lib/cache',
		'uflib_classwithservice'     => 'lib/classwithservice',
		'uflib_configuration'        => 'lib/configuration',
		'uflib_daonormalizer'        => 'lib/daonormalizer',
		'uflib_daovalidator'         => 'lib/daovalidator',
		'uflib_datastorage'          => 'lib/datastorage',
		'uflib_db_drivermysql'       => 'lib/db/drivermysql',
		'uflib_db_driverpostgresql'  => 'lib/db/driverpostgresql',
		'uflib_db_driverredis'       => 'lib/db/driverredis',
		'uflib_db_driver'            => 'lib/db/driver',
		'uflib_db_query'             => 'lib/db/query',
		'uflib_file'                 => 'lib/file',
		'uflib_form'                 => 'lib/form',
		'uflib_formold'              => 'lib/formold',
		'uflib_http'                 => 'lib/http',
		'uflib_localegettext'        => 'lib/localegettext',
		'uflib_locale'               => 'lib/locale',
		'uflib_logtomemory'          => 'lib/logtomemory',
		'uflib_logtonull'            => 'lib/logtonull',
		'uflib_logwrapper'           => 'lib/logwrapper',
		'uflib_messages'             => 'lib/messages',
		'uflib_normalize'            => 'lib/normalize',
		'uflib_request'              => 'lib/request',
		'uflib_services'             => 'lib/services',
		'uflib_session'              => 'lib/session',
		'uflib_strings'              => 'lib/strings',
		'uflib_valid'                => 'lib/valid',
		'uflib_wiki_xhtml'           => 'lib/wiki/xhtml',
		'uflib_wiki'                 => 'lib/wiki',
		'uftpl_core_log'             => 'tpl/core/log',
		'uftpl_html'                 => 'tpl/html',
		'uftpl_log'                  => 'tpl/log',
		'uftpl_form'                 => 'tpl/form',
	);

	/**
	 * inicjalizacja srodowiska
	 */
	static public function boot() {
		self::$services = self::factory('UFlib_Services');
		define('NOW', time());
		// TODO
		//set_error_handler(array('UFraCore', 'errorHandler'));
	}


	/**
	 * fabryka obiektow
	 * 
	 * @param $className - nazwa klasy
	 * @param $param - parametry konstruktora klasy - moze ich byc wiele
	 * @return object
	 */
	static public function &factory() {
		$params = func_get_args();
		$className = $params[0];
		unset($params[0]);
		$reflection = new ReflectionClass($className);
		if (count($params)) {
			$obj = $reflection->newInstanceArgs($params);
		} else {
			$obj = $reflection->newInstance();
		}
		return $obj;
	}

	/**
	 * obiekt wspoldzielony
	 * 
	 * @param $className - nazwa klasy
	 * @param $param - parametry konstruktora klasy - moze ich byc wiele
	 * @return object
	 */
	static public function &shared() {
		$params = func_get_args();
		$className = $params[0];

		if (isset(self::$sharedClasses[$className])) {
			$obj =& self::$sharedClasses[$className];
		} else {
			$obj = call_user_func_array(array('UFraCore', 'factory'), $params);
			self::$sharedClasses[$className] =& $obj;
		}
		return $obj;
	}

	/**
	 * wszystkie uslugi
	 * @return UFlib_Services
	 */
	static public function &services() {
		return self::$services;
	}

	/**
	 * include'uje konkretny plik z klasa
	 * 
	 * @param string $classFile - nazwa pliku, ale bez rozszerzenia
	 * @return bool - czy udalo sie zainclude'owac?
	 */
	static protected function includeClass($classFile) {
		$classFile .= '.php';
		if (file_exists($classFile)) {
			return (bool)include_once($classFile);
		} else {
			return false;
		}
	}

	/**
	 * ladowanie plikow z klasami
	 * 
	 * @param string - $className 
	 * @return bool - czy udalo sie zaladowac?
	 */
	static public function autoload($className) {
		$classFile = strtolower($className);
		$classFile = substr($classFile, 2);

		if (isset(self::$classMap[$classFile])) {
			$classFile = self::$classMap[$classFile];
		} else {
			$classFile = str_replace('_','/',$classFile);
			$classFile = str_replace('//','/_',$classFile);
		}
		return (bool)self::includeClass(UFDIR_CORE.$classFile);
	}

	/**
	 * obsluga bledow
	 * 
	 * @param int $errNo - nr/poziom bledu
	 * @param string $errStr - komunikat bledu
	 * @param string $errFile - plik z bledem
	 * @param int $errLine - bledna linia
	 */
	static public function errorHandler($errNo, $errStr, $errFile, $errLine) {
		$tmp = func_get_args();
		$tmp['time'] = microtime(true);
		self::$logs[] = $tmp;
	}

	/**
	 * wszystkie zerbane do tej pory logi
	 * @return array
	 */
	static public function logs() {
		return self::$logs;
	}

	static protected function log($txt, $level) {
		$dbg = debug_backtrace();
		$dbg = $dbg[1];
		self::errorHandler($level, $txt, $dbg['file'], $dbg['line']);
	}

	static public function debug($txt) {
		self::log($txt, E_USER_NOTICE);
	}

	static public function warning($txt) {
		self::log($txt, E_USER_WARNING);
	}

	static public function error($txt) {
		echo $txt;
		self::log($txt, E_USER_ERROR);
	}
}
