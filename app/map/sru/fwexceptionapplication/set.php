<?php
/**
 * dodanie wniosku
 */
class UFmap_Sru_FwExceptionApplication_Set
extends UFmap {
	protected $columns = array(
		'sspgOpinion'		=> 'sspg_opinion',
		'sspgComment'		=> 'sspg_comment',
		'sspgOpinionAt'		=> 'sspg_opinion_at',
		'sspgOpinionBy'		=> 'sspg_opinion_by',
		'skosOpinion'		=> 'skos_opinion',
		'skosComment'		=> 'skos_comment',
		'skosOpinionAt'		=> 'skos_opinion_at',
		'skosOpinionBy'		=> 'skos_opinion_by',
	);
	protected $columnTypes = array(
		'sspgOpinion'		=> self::BOOL,
		'sspgComment'		=> self::NULL_TEXT,
		'sspgOpinionAt'		=> self::TS,
		'sspgOpinionBy'		=> self::INT,
		'skosOpinion'		=> self::BOOL,
		'skosComment'		=> self::NULL_TEXT,
		'skosOpinionAt'		=> self::TS,
		'skosOpinionBy'		=> self::INT,
	);
	protected $tables = array(
		'' => 'fw_exception_applications',
	);

	protected $valids = array(
	);
	
	protected $pk = 'id';
}

