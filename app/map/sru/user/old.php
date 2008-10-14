<?
/**
 * wyciagniecie pojedynczego bylego uzytkownika
 */
class UFmap_Sru_User_Old
extends UFmap {

	protected $columns = array(
		'email'        => 'email',
	);
	protected $columnTypes = array(
		'email'        => self::TEXT,
	);
	protected $tables = array(
		'u' => 'users_old',
	);
	protected $pk = 'id';
}
