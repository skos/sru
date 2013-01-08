<?php
class UFtpl_Log
extends UFtpl {


	public function full(array $d) {
		echo '<pre>';
		print_r($d['logs']->write('full'));
		echo '</pre>';
	}
}
