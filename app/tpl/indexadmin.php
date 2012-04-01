<?
/**
 * szablon czesci administracyjnej
 */
class UFtpl_IndexAdmin
extends UFtpl_Common {

	public function index(array $d) {
		header('Content-Type: text/html; charset=UTF-8');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex,nofollow,noarchive" />
<link rel="stylesheet" href="<?=UFURL_BASE;?>/i/css/style.css" type="text/css" media="screen" />
<link rel="shortcut icon" href="<?=UFURL_BASE;?>/i/img/favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="<?=UFURL_BASE;?>/i/jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?=UFURL_BASE;?>/i/jquery/jquery.tools.min.js"></script>
<script type="text/javascript" src="<?=UFURL_BASE;?>/i/jquery/jquery.contextMenu.js"></script>
<title><?=$d['title'];?></title>
</head>
<body>
<div id="body">
<div id="head">
<h1><a href="<?=UFURL_BASE;?>/admin/"><img src="<?=UFURL_BASE;?>/i/img/skoslogo.png" alt="logo SKOS"/>&nbsp;Admin</a></h1>
</div><!-- head -->
<?=$d['menuAdmin'];?>
<?=$d['adminBar'];?>
<div id="main">
<?=$d['body'];?>
</div><!-- main -->
<div id="foot">
&copy;&nbsp;<a href="mailto:adnet@ds.pg.gda.pl">SKOS PG</a>
</div><!-- foot -->
</div><!-- body -->
<script type="text/javascript">
$("#main img[title]").tooltip({ position: "center right"});
</script>
<?
$stop = microtime(true);
$start = $this->_srv->get('msg')->get('timeStart');
$boot = $this->_srv->get('msg')->get('timeBoot');
echo '<!-- boot: '.number_format(1000*($boot-$start), 1).'ms, total: '.number_format(1000*($stop-$start), 1).'ms -->';
?>
<?=$d['logs'];?>
</body>
</html><?
	}
}
