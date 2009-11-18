<?php
/**
 * kara
 */
class UFbean_SruAdmin_Penalty
extends UFbeanSingle {

	const TYPE_WARNING = 1;
	const TYPE_COMPUTER = 2;
	const TYPE_COMPUTERS = 3;

	protected $notifyAbout = array(
		'reason',
		'endAt',
	);

	public function notifyByEmail() {
		// nie mozna tego zrobic w jednej linii, bo php rzuca bledem "Can't use
		// function return value in write context"
		$ans = array_intersect(array_keys($this->dataChanged), $this->notifyAbout);
		return !empty($ans);
	}
}
