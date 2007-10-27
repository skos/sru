<?
error_reporting(E_ALL|E_STRICT);
$start = microtime(true);
define('UFURL_BASE', '');
define('UFDIR_CORE', realpath(getcwd().'/../core/').'/');
define('UFDIR_APP', realpath(getcwd().'/../app/').'/');

include(UFDIR_APP.'ufra.php');

UFra::boot();
$boot = microtime(true);
$srv =& UFra::services();
$srv->get('msg')->set('timeStart', $start);
$srv->get('msg')->set('timeBoot', $boot);
$front = UFra::factory('UFctl_Front');
$front->go();
