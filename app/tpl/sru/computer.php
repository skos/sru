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
			echo '<li><a href="'.$url.$c['id'].'">'.$c['host'].' <small>'.$c['ip'].'/'.$c['mac'].'</small></a> <span>'.date(self::TIME_YYMMDD, $c['availableTo']).'</span></li>';
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
		$ip = explode('.', $d['ip']);
		$tag = substr(md5('haha'.$ip[2].$ip[3]), 0, 5);
		echo '<p><a href="https://sru.ds.pg.gda.pl/lanstats/?ip='.$ip[2].'.'.$ip[3].'"><img src="https://sru.ds.pg.gda.pl/lanstats/153.19.'.$ip[2].'/'.str_pad($ip[3], 3, '0', STR_PAD_LEFT).'.'.$tag.'.png" alt="Statystyki transferów" /></a></p>';
	}

	public function formEdit(array $d) {
		$form = UFra::factory('UFlib_Form', 'computerEdit', $d, $this->errors);

		echo '<h1>'.$d['host'].'.ds.pg.gda.pl</h1>';
		$form->mac('MAC');
	}

	public function formAdd(array $d) {
		$form = UFra::factory('UFlib_Form', 'computerAdd', $d, $this->errors);

		$form->host('Nazwa');
		$form->mac('MAC');
	}

	public function formDel(array $d) {
		$form = UFra::factory('UFlib_Form');
		$form->confirm('Tak, chcę wyrejestrować ten komputer', array('type'=>$form->CHECKBOX, 'name'=>'computerDel[confirm]', 'value'=>'1'));
	}
}
