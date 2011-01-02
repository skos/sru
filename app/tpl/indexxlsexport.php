<?
/**
 * szablon strony w formacie xls
 */
class UFtpl_IndexXlsExport
extends UFtpl_Common {

	public function index(array $d) {
		header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("content-disposition: attachment;filename=".$d['title'].".xls");

		echo '<html>';
		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
		echo '<body>';
		echo $d['body'];
		echo '</body>';
		echo '</html>';
	}
}
