<?

require_once(UFDIR_CORE.'ufra.php');

class UFra
extends UFraCore{
	
	static protected $booted = false;

	static public function boot() {
		set_error_handler(array('UFra', 'phpError'));
		if (self::$booted) {
			return;
		}
		parent::boot(); 
		self::debug('Start');

		$conf = self::shared('UFconf_Core');

		date_default_timezone_set($conf->timezone);
		
		$db = $conf->db;
		$tmp = self::factory('UFlib_Db_Driver'.$conf->db['driver'], $db);

		self::$services->set('conf', $conf);
		self::$services->set('db', $tmp);
		$req = self::shared('UFlib_Request');
		$msgNext = self::factory('UFlib_Messages');
		$session = self::shared('UFlib_Session');
		if ($session->is('msgNext') && $session->msgNext instanceof UFlib_Messages) {
			$msg = $session->msgNext;
			$session->del('msgNext');
		} else {
			$msg = self::shared('UFlib_Messages');
		}
		$session->msgNext = $msgNext;
		$acl = self::shared('UFacl');
		self::$services->set('req', $req);
		self::$services->set('msg', $msg);
		self::$services->set('msgNext', $msgNext);
		self::$services->set('session', $session);
		self::$services->set('acl', $acl);
	}

	static public function phpError($errNo, $errStr, $errFile, $errLine) {
		if (strpos($errStr, 'snmp') !== false) {
			file_put_contents(UFDIR_APP.'var/log/snmp-errors.log', date('c')." $errFile:$errLine $errStr\n", FILE_APPEND);
		} else {
			file_put_contents(UFDIR_APP.'var/log/ufra-errors.log', date('c')." $errFile:$errLine $errStr\n", FILE_APPEND);
		}
		self::errorHandler($errNo, $errStr, $errFile, $errLine);
	}

	static public function error($txt) {
		file_put_contents(UFDIR_APP.'var/log/ufra-errors.log', date('c')." $txt\n", FILE_APPEND);
		self::log($txt, E_USER_ERROR);
	}

	static public function autoload($className) {
		$classFile = strtolower($className);
		if (isset(self::$classMap[$classFile])) {
			$classFile = UFDIR_CORE.self::$classMap[$classFile];
		} else {
			$classFile = substr($classFile, 2);
			$classFile = str_replace('_','/',$classFile);
			$classFile = UFDIR_APP.str_replace('//','/_',$classFile);
		}
		return self::includeClass($classFile);
	}
}

function __autoload($class) {
	return UFra::autoload($class);
}
