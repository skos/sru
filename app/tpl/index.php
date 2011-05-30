<?
/**
 * szablon strony
 */
class UFtpl_Index
extends UFtpl_Common {

	public function index(array $d) {
		header('Content-Type: text/html; charset=UTF-8');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="robots" CONTENT="noindex,nofollow,noarchive" />
<link rel="stylesheet" href="<?=UFURL_BASE;?>/i/css/style.css" type="text/css" media="screen" />
<link rel="shortcut icon" href="<?=UFURL_BASE;?>/i/img/favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="<?=UFURL_BASE;?>/i/jquery/jquery.tools.min.js"></script>
<title><?=$d['title'];?></title>
</head>
<body>
<div id="body">
<div id="head">
<h1><a href="<?=UFURL_BASE;?>/"><img src="<?=UFURL_BASE;?>/i/img/skoslogo.png" alt="logo SKOS"/>&nbsp;<abbr title="System Rejestracji Użytkowników">SRU</abbr></a></h1>
</div><!-- head -->
<ul id="nav">
<li><a href="http://skos.ds.pg.gda.pl/skos/wiki/regulamin">Regulamin</a></li>
<li><a href="<?=UFURL_BASE;?>/sru/">Użytkownik</a></li>
<li><a href="<?=UFURL_BASE;?>/admin/">SKOS</a></li>
<li><a href="<?=UFURL_BASE;?>/walet/">Walet</a></li>
</ul>
<div id="main">
<?=$d['body'];?>
</div><!-- main -->
<div id="foot">
&copy;&nbsp;<a href="mailto:adnet@ds.pg.gda.pl">SKOS PG</a>
</div><!-- foot -->
</div><!-- body -->
<?
$stop = microtime(true);
$start = $this->_srv->get('msg')->get('timeStart');
$boot = $this->_srv->get('msg')->get('timeBoot');
echo '<!-- boot: '.number_format(1000*($boot-$start), 1).'ms, total: '.number_format(1000*($stop-$start), 1).'ms -->';
//echo '<pre>'.print_r(UFra::logs(), true).'</pre>';
?>
</body>
</html><?
	}
}
