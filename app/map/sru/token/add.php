<?
/**
 * dodanie tokenu
 */
class UFmap_Sru_Token_Add
extends UFmap {

	protected $columns = array(
		'token'          => 'token',
		'userId'         => 'user_id',
		'validTo'        => 'valid_to',
		'type'           => 'type',
	);
	protected $columnTypes = array(
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
