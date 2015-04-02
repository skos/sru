<?
/**
 * szablon funkcji usera
 */
class UFtpl_Sru_UserFunction
extends UFtpl_Common {

	static public $functions = array(
		1 => 'Przewodniczacy RO',
		11 => 'Przewodniczący RM',
		12 => 'Członek RM',
		21 => 'Opiekun',
		22 => 'Członek komisji wyborczej DS',
	);
	
	public function listOwn(array $d) {
		echo '<ul>';
		foreach ($d as $function) {
			echo '<li><span class="userData">'.UFtpl_Sru_UserFunction::$functions[$function['functionId']].' '.$function['comment'].'</span>';
                }
		echo '</ul>';
        }
}
