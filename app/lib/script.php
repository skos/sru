<?php
/* Wszelkie skrypty JS */
class UFlib_Script {
    
    public static function focus($id){?>        
        <script type="text/javascript">
            document.getElementById('<?php echo $id; ?>').focus();
        </script>
    <?}
    
    public static function SruAdmin_Switch_changeVisibility(){
?><script type="text/javascript">
function changeVisibility() {
	var div = document.getElementById('switchMore');
	if (div.sruHidden != true) {
		div.style.display = 'none';
		div.sruHidden = true;
	} else {
		div.style.display = 'block';
		div.sruHidden = false;
	}
}
var container = document.getElementById('switchMoreSwitch');
var button = document.createElement('a');
button.onclick = function() {
	changeVisibility();
}
var txt = document.createTextNode('Szczegóły');
button.appendChild(txt);
container.appendChild(button);
changeVisibility();
</script><?
        
    }

}
?>
