<?php
class UFex_Db_QueryFailed
extends UFex {

	// numery bledow sa takie same, jak nr bledow mysql-a
	
	const ERR_CONNECT    = 1045;	// blad polaczenia z serwerem
	const ERR_NODBSEL    = 1046;	// nie wybrano bazy
	const ERR_NODB       = 1049;	// brak bazy
	const ERR_NOCOL      = 1054;	// brak kolumny
	const ERR_NOTABLE    = 1146;	// brak tabeli
	const ERR_DUPLICATED = 1062;	// wartosc juz istnieje
	const ERR_NOFOREIGN  = 1216;	// brak klucza obcego
	const ERR_CONSTRAINT = 1217;	// klucz obcy naruszony
}
