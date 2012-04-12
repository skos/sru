<?php
/* Wszelkie skrypty JS */
class UFlib_Script {
    
    public static function focus($id){?>        
        <script type="text/javascript">
            document.getElementById('<?php echo $id; ?>').focus();
        </script>
    <?}
    
    public static function switchMoreChangeVisibility(){
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
	
			
		/**
		 * Wyświetla menu kontekstowe edycji portu
		 */
		public static function displaySwitchPortMenu(array $switchUrls) {
			echo '<ul id="switchContexMenu" class="contextMenu">
    <li class="editSwitchContexMenu">
        <a href="#edit">Edytuj</a>
    </li>
	<li class="macSwitchContexMenu">
        <a href="#mac">Pokaż adresy MAC</a>
    </li>
</ul>';

			foreach ($switchUrls as $swId => $swUrl) {
?><script type="text/javascript">
$(document).ready( function() {
    $("#switchPortsT<? echo $swId ?> td").contextMenu({
        menu: "switchContexMenu"
    },
        function(action, el, pos) {
			if (action == "edit") {
				window.location = "<? echo $swUrl ?>/port/" + $(el).attr("id") + "/:edit";
			} else if (action == "mac") {
				window.location = "<? echo $swUrl ?>/port/" + $(el).attr("id") + "/macs";
			}
    });
});
</script><?
			}
		}

}
?>
