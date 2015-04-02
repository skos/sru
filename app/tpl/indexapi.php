<?
/**
 * szablon strony api
 */
class UFtpl_IndexApi
extends UFtpl_Common {

	public function index(array $d) {
		header('Content-Type: text/plain; charset=UTF-8');
		echo $d['body'];
	}
}
