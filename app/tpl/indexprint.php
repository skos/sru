<?
/**
 * szablon czesci Waleta - wydruki
 */
class UFtpl_IndexPrint
extends UFtpl_Common {

	public function index(array $d) {
		header('Content-Type: text/html; charset=UTF-8');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="robots" CONTENT="noindex,nofollow,noarchive"> 
<link rel="stylesheet" href="<?=UFURL_BASE;?>/i/style.css" type="text/css" media="screen" />
<title><?=$d['title'];?></title>
</head>
<body>
<div id="body">
<div id="main">
<?=$d['body'];?>
</div><!-- main -->
</div><!-- body -->
</body>
<script type="text/javascript">
window.print();
</script>
</html><?
	}
}
