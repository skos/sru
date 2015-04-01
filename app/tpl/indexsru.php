<?
/**
 * szablon strony
 */
class UFtpl_IndexSru
extends UFtpl_Common {

	public function index(array $d) {
		header('Content-Type: text/html; charset=UTF-8');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex,nofollow,noarchive" />
<link rel="stylesheet" href="<?=UFURL_BASE;?>/i/css/style.css" type="text/css" media="screen" />
<? echo (UFra::shared('UFconf_Sru')->prodInstance ? '' : '<link rel="stylesheet" href="'.UFURL_BASE.'/i/css/test.css" type="text/css" media="screen" />'); ?>
<link rel="stylesheet" href="<?=UFURL_BASE;?>/i/jquery/css/smoothness/jquery-ui.custom.css" type="text/css" />	
<link rel="shortcut icon" href="<?=UFURL_BASE;?>/i/img/favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="<?=UFURL_BASE;?>/i/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=UFURL_BASE;?>/i/jquery/jquery-ui.custom.min.js"></script>
<script type="text/javascript" src="<?=UFURL_BASE;?>/i/jquery/development-bundle/ui/i18n/jquery.ui.datepicker-pl.js"></script>
<script type="text/javascript" src="<?=UFURL_BASE;?>/i/js/datepickerInit.js"></script>
<script type="text/javascript" src="<?=UFURL_BASE;?>/i/jquery/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?=UFURL_BASE;?>/i/js/jqsimplemenu.js"></script>
<title><?=$d['title'];?></title>
</head>
<body>
<div id="body">
<div id="head">
<div id="logoBar"><h1><a href="<?=UFURL_BASE;?>/sru/"><img src="<?=UFURL_BASE;?>/i/img/skoslogo.png" alt="logo SKOS"/>&nbsp;<abbr title="System Rejestracji Użytkowników">SRU</abbr></a></h1></div>
<div class="userBar"><?=$d['userBar'];?></div>
</div><!-- head -->
<?=$d['userMainMenu'];?>
<div id="socialBar">
<ul id="socialSidebar">
<li class="facebook">
<a href="http://facebook.com/skospg"><img alt="facebook" src="<?=UFURL_BASE;?>/i/img/socialSidebar_facebook.png" title="<? echo (_("Znajdź nas na Facebooku"));?>" /></a>
</li>
<li class="gplus">
<a href="https://plus.google.com/101381300119889439141/posts"><img alt="gplus" src="<?=UFURL_BASE;?>/i/img/socialSidebar_gplus.png" title="<? echo (_("Znajdź nas na Google+"));?>" /></a>
</li>
</ul>
</div>
<div id="main">
<?=$d['body'];?>
</div><!-- main -->
<div id="foot">
&copy;&nbsp;<a href="http://skos.ds.pg.gda.pl">SKOS PG</a>
</div><!-- foot -->
</div><!-- body -->
<script type="text/javascript">
$(function() {
	$( document ).tooltip();
});
$('form').submit(function(){
	$('input[type=submit]', this).attr('disabled', 'disabled');
});
</script>
<?
$stop = microtime(true);
$start = $this->_srv->get('msg')->get('timeStart');
$boot = $this->_srv->get('msg')->get('timeBoot');
echo '<!-- boot: '.number_format(1000*($boot-$start), 1).'ms, total: '.number_format(1000*($stop-$start), 1).'ms -->';
//echo '<pre>'.print_r(UFra::logs(), true).'</pre>';
?>
<?=$d['logs'];?>
</body>
</html><?
	}
}

