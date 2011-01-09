<?
/**
 * szablon kary
 */
class UFtpl_SruAdmin_PenaltyTemplate
extends UFtpl_Common {

	protected $errors = array(
		'duration' => 'Podaj długość',
		'amnesty' => 'Podaj minimalną długość',
		'title/textMin' => 'Podaj nazwę',
		'title/duplicated' => 'Podana nazwa już istnieje',
		'description/textMin' => 'Podaj opis',
	);

	public function listEdit(array $d) {
		$url = $this->url(2);
		$lastTemplate = '-';
		foreach ($d as $t) {
			if ($lastTemplate != '-' && substr($t['title'], 0, 3) != substr($lastTemplate, 0, 3)) {
				echo '<li><hr/></li>';
			}
			echo '<li><h3><a href="'.$url.'/'.$t['id'].'/:edit">'.$t['title'].'</a></h3><p>'.$t['description'].' <em>('.$t['duration'].' dni / '.$t['amnesty'].' dni)</em></p></li>';
			$lastTemplate = $t['title'];
		}
		echo '<li><hr/></li>';
	}

	public function choose(array $d) {
		$url = $this->url();
		$lastTemplate = '-';
		foreach ($d as $t) {
			if ($lastTemplate != '-' && substr($t['title'], 0, 3) != substr($lastTemplate, 0, 3)) {
				echo '<li><hr/></li>';
			}
			echo '<li><h3><a href="'.$url.'/template:'.$t['id'].'">'.$t['title'].'</a></h3><p>'.$t['description'].' <em>('.$t['duration'].' dni / '.$t['amnesty'].' dni)</em></p></li>';
			$lastTemplate = $t['title'];
		}
		echo '<li><hr/></li>';
		echo '<li><h3><a href="'.$this->url().'/template:0">Inne</a></h3><p>Żadne z powyższych</p></li>';
	}

	public function formAdd(array $d) {
		$form = UFra::factory('UFlib_Form', 'penaltyTemplateAdd', $d, $this->errors);

		echo $form->_fieldset();
		echo $form->title('Nazwa');
		echo $form->description('Opis', array('type'=>$form->TEXTAREA, 'rows'=>2));
		echo $form->duration('Długość (dni)');
		echo $form->amnesty('Min. długość (dni)');
		echo $form->reason('Opis dla użytkownika',  array('type'=>$form->TEXTAREA, 'rows'=>5));
		echo $form->reasonEn('Opis dla użytkownika en',  array('type'=>$form->TEXTAREA, 'rows'=>5));
		echo $form->typeId('Typ', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(UFtpl_SruAdmin_Penalty::$penaltyTypes),
		));	
		echo $form->active('Aktywny', array('type'=>$form->CHECKBOX));
	}

	public function formEdit(array $d) {
		$form = UFra::factory('UFlib_Form', 'penaltyTemplateEdit', $d, $this->errors);

		echo $form->_fieldset();
		echo $form->description('Opis', array('type'=>$form->TEXTAREA, 'rows'=>2));
		echo $form->duration('Długość (dni)');
		echo $form->amnesty('Min. długość (dni)');
		echo $form->reason('Opis dla użytkownika',  array('type'=>$form->TEXTAREA, 'rows'=>5));
		echo $form->reasonEn('Opis dla użytkownika en',  array('type'=>$form->TEXTAREA, 'rows'=>5));
		echo $form->typeId('Typ', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(UFtpl_SruAdmin_Penalty::$penaltyTypes),
		));	
		echo $form->active('Aktywny', array('type'=>$form->CHECKBOX));
	}

}
