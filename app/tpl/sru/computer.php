<?
/**
 * szablon beana komputera
 */
class UFtpl_Sru_Computer
extends UFtpl {
	
	protected $errors = array(
		'host' => 'Nieprawidłowa nazwa',
		'host/duplicated' => 'Nazwa jest już zajęta',
		'host/textMin' => 'Nazwa jest za krótka',
		'host/textMax' => 'Nazwa jest zbyt długa',
		'host/regexp' => 'Zawiera niedowzolone znaki',
		'mac' => 'Nieprawidłowy format',
		'mac/duplicated' => 'MAC jest już zajęty',
	);
	
	public function listOwn(array $d) {
		$url = $this->url(1).'/';
		foreach ($d as $c) {
			if ($c['active']) {
				$host = '<em>'.$c['host'].'</em>';
			} else {
				$host = $c['host'];
			}
			echo '<li><a href="'.$url.$c['id'].'">'.$host.' <small>'.$c['ip'].'/'.$c['mac'].'</small></a> <span>'.date(self::TIME_YYMMDD, $c['availableTo']).'</span></li>';
		}
	}

	public function titleDetails(array $d) {
		echo 'Komputer "'.$d['host'].'"';
	}

	public function detailsOwn(array $d) {
		echo '<h1>'.$d['host'].'.ds.pg.gda.pl</h1>';
		echo '<p><em>MAC:</em> '.$d['mac'].'</p>';
		echo '<p><em>IP:</em> '.$d['ip'].'</p>';
		echo '<p><em>Rejestracja do:</em> '.date(self::TIME_YYMMDD, $d['availableTo']).'</p>';
		echo '<p><em>Miejsce:</em> '.$d['locationAlias'].' ('.$d['dormitoryName'].')</p>';
		echo '<p><em>Liczba kar:</em> '.$d['bans'].'</p>';
	}

	public function formEdit(array $d) {
		$form = UFra::factory('UFlib_Form', 'computerEdit', $d, $this->errors);

		echo '<p><em>Nazwa:</em> '.$d['host'].'</p>';
		$form->mac('MAC');
		echo '<p><em>IP:</em> '.$d['ip'].'</p>';
		echo '<p><em>Rejestracja do:</em> '.date(self::TIME_YYMMDD, $d['availableTo']).'</p>';
		echo '<p><em>Miejsce:</em> '.$d['locationAlias'].' ('.$d['dormitoryName'].')</p>';
	}

	public function formAdd(array $d) {
	print_r( $this->_srv->get('msg'));
		$form = UFra::factory('UFlib_Form', 'computerAdd', $d, $this->errors);

		$form->host('Nazwa');
		$form->mac('MAC');
	}

	public function formDel(array $d) {
	}
}
