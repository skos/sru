<?
/**
 * szablon modulu sru
 */
class UFtpl_Sru
extends UFtpl {

	public function titleLogin() {
		echo 'Zaloguj się';
	}

	public function login(array $d) {
		$form = UFra::factory('UFlib_Form');

		$form->_start();
		$form->_fieldset('Zaloguj się');
		echo $d['user']->write('formLogin');
		$form->_submit('Zaloguj');
		echo ' <a href="'.$this->url(0).'/create">Załóż konto</a>';
		$form->_end();
		$form->_end(true);
	}

	public function titleUserAdd() {
		echo 'Załóż konto';
	}

	public function userAdd(array $d) {
		$form = UFra::factory('UFlib_Form');
		print_r($this->_srv->get('msg'));

		$form->_start();
		$form->_fieldset('Załóż konto');
		if ($this->_srv->get('msg')->get('userAdd/ok')) {
			UFtpl_Html::msgOk('Konto zostało założone');
		}
		echo $d['user']->write('formAdd', $d['dormitories'], $d['faculties']);
		$form->_submit('Załóż');
		$form->_end();
		$form->_end(true);
	}
}
