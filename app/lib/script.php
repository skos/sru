<?php
/* Wszelkie skrypty JS */
class UFlib_Script {
    
    public static function focus($id){?>        
        <script type="text/javascript">
            document.getElementById('<?php echo $id; ?>').focus();
        </script>
    <?}

}
?>
