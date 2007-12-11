<?php
/**
 * akademik
 */
class UFtpl_SruAdmin_Dorm
extends UFtpl {

	public function listDorms(array $d) {
		$url = $this->url(0).'/dormitories/';
		
		$lastDom = '-';

		echo '<ul>';
		
		foreach ($d as $c)
		{
			echo '<li><a href="'.$url.$c['alias'].'">'.$c['name'].'</a></li>';			
		}
		echo '</ul>';
	}

	public function titleDetails(array $d) {
		echo $d['name'];
	}
	public function details(array $d) {
		
		$url = $this->url(0);
		echo '<h2>'.$d['name'].'</h2>';
						
	}	
/*
	public function titleAdd(array $d) {
		echo 'Dodanie nowego administratora';
	}		
	public function formAdd(array $d, $dormitories) {
		if (!isset($d['typeId'])) {
			$d['typeId'] = 3;
		}
		$form = UFra::factory('UFlib_Form', 'adminAdd', $d, $this->errors);

		$form->_fieldset();
		$form->login('Login');
		$form->password('Hasło', array('type'=>$form->PASSWORD));
		$form->password2('Powtórz hasło', array('type'=>$form->PASSWORD));
		$form->name('Nazwa'); 
		$form->typeId('Uprawnienia', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($this->adminTypes),
		));
		$form->email('E-mail');
		$form->phone('Telefon');
		$form->gg('GG');
		$form->jid('Jabber');
		$form->address('Adres');

		$tmp = array();
		foreach ($dormitories as $dorm) {
			$tmp[$dorm['id']] = $dorm['name'];
		}
		$tmp['-'] = 'N/D';
		$form->dormitoryId('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
		));		
	}

	public function formEdit(array $d, $dormitories, $advanced=false) {

		$form = UFra::factory('UFlib_Form', 'adminEdit', $d, $this->errors);

		$form->_fieldset();
		
		$form->name('Nazwa'); 
		
		$form->password('Hasło', array('type'=>$form->PASSWORD));
		$form->password2('Powtórz hasło', array('type'=>$form->PASSWORD));

		if($advanced)
		{
			$form->typeId('Uprawnienia', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize($this->adminTypes),
			));	
			$form->active('Aktywny', array('type'=>$form->CHECKBOX) );

		}

		$form->_end();
		
		$form->_fieldset();
		$form->email('E-mail');
		$form->phone('Telefon');
		$form->gg('GG');
		$form->jid('Jabber');
		$form->address('Adres');

		$tmp = array();
		foreach ($dormitories as $dorm) {
			$tmp[$dorm['id']] = $dorm['name'];
		}
		$tmp[''] = 'N/D';
		$form->dormitoryId('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));		
	}
*/
}
