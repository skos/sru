<?
/**
 * wybranie pojedynczego tokenu
 */
class UFmap_Sru_Token_Get
extends UFmap {

	protected $columns = array(
		'id'             => 'id',
		'token'          => 'token',
		'userId'         => 'user_id',
		'validTo'        => 'valid_to',
		'type'           => 'type',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'token'          => self::TEXT,
		'userId'         => self::INT,
		'validTo'        => self::TS,
		'type'           => self::INT,
	);
	protected $tables = array(
		'' => 'users_tokens',
	);
	protected $pk = 'id';
}
