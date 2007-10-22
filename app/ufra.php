<?

require_once(UFDIR_CORE.'ufra.php');

class UFra
extends UFraCore{
	
	static protected $booted = false;

	static public function boot() {
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

		define('NOW', time());
	}

	static public function autoload($className) {
		$classFile = strtolower($className);
		$classFile = substr($classFile, 2);
		$classFile = str_replace('_','/',$classFile);
		$classFile = str_replace('//','/_',$classFile);
		
		$tmp = explode('/', $classFile);
		switch ($tmp[0]) {
			case 'ex':
			case 'lib':
				// tych klas najpierw szukamy w core, a potem w aplikacji
				if (parent::autoload($className)) {
					return true;
				} else {
					return self::includeClass(UFDIR_APP.$classFile);
				}
				break;
			default: 
				if (!self::includeClass(UFDIR_APP.$classFile)) {
					return parent::autoload($className);
				}
				break;
		}
	}

}

function __autoload($class) {
	return UFra::autoload($class);
}
