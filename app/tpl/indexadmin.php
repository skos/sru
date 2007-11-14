<?
/**
 * szablon czesci administracyjnej
 */
class UFtpl_IndexAdmin
extends UFtpl {

	public function index(array $d) {
		header('Content-Type: text/html; charset=UTF-8');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="<?=UFURL_BASE;?>/i/style.css" type="text/css" media="screen" />
<title><?=$d['title'];?></title>
</head>
<body>
<div id="body">
<div id="head">
<h1><a href="<?=UFURL_BASE;?>"><abbr title="Sieć Komputerowa Osiedla Studenckiego">SKOS</abbr></a></h1>
</div><!-- head -->
<?=$d['menuAdmin'];?>
<div id="main">
<?=$d['body'];?>
</div><!-- main -->
<div id="foot">
<a href="mailto:hrynek@hrynek.com">Maciej "HryneK" Hryniewicz</a>
</div><!-- foot -->
</div><!-- body -->
<pre>
<?
$stop = microtime(true);
$start = $this->_srv->get('msg')->get('timeStart');
$boot = $this->_srv->get('msg')->get('timeBoot');
echo '<!-- boot: '.number_format(1000*($boot-$start), 1).'ms, total: '.number_format(1000*($stop-$start), 1).'ms -->';
echo print_r(UFra::logs(), true);
?>
</pre>
</body>
</html><?
	}
}
