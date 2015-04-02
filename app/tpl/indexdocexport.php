<?
/**
 * szablon strony w formacie doc
 */
class UFtpl_IndexDocExport
extends UFtpl_Common {

	public function index(array $d) {
		header("Content-Type: application/vnd.msword; charset=UTF-8");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("content-disposition: attachment;filename=".$d['title'].".doc");

		echo '<html>';
		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
		echo '<body>';
		echo $d['body'];
		echo '</body>';
		echo '</html>';
	}
}
