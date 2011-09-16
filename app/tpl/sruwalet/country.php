<?
/**
 * template kraju
 */
class UFtpl_SruWalet_Country
extends UFtpl_Common {

	public function nations(array $d) {
		echo '<ul>';
		foreach ($d as $c) {
			echo '<li><span id="'.$c['id'].'">'.$c['nationality'].'</span><span id="'.$c['id'].'_result"> (użytkowników: '.$c['nationalityUsers'].')</li>';
		}
		echo '</ul>';
		
?>
<script type="text/javascript" src="<?=UFURL_BASE;?>/i/js/prototype.js"></script>
<script type="text/javascript" src="<?=UFURL_BASE;?>/i/js/instantedit.js"></script>
<script type="text/javascript">
Event.observe(window, 'load', init, false);

function init(){
<?
	foreach ($d as $c) {
?>
	makeEditable('<?=$c['id']?>');
<?
	}
?>
}

function saveChanges(obj){
	var new_content	=  $F(obj.id+'_edit');
	cleanUp(obj, true);
	if (new_content == '') {
		return;
	}

	var success	= function(t){editComplete(t, obj);}
	var failure	= function(t){editFailed(t, obj);}

  	var url = '<?=UFURL_BASE?>/walet/nations/quicksave/' + obj.id + '/' + new_content;
	new Ajax.Request(url, {method:'post', onSuccess:success, onFailure:failure});
}
</script>
<?
	}
}
