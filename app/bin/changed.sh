#!/bin/bash

function help() {
	cat << EOH
$(basename $0) check_file_1 [check_file_2] ...
Sprawdza, czy pliki ulegly zmianie od ostatniego sprawdzenia. Wynik zwracany
jest w postaci kodu wyjscia:
  0 - jezeli wszystkie pozostaly jednakowe
  1 - ktorykolwiek z plikow zmienil sie
  2 - nie podano plikow do sprawdzenia
  3 - brak ktoregos z plikow
EOH
	exit 2
}

if [[ -z "$1" ]]; then
	help;
fi

code=1

for file in "$@"; do
	dir=$(dirname $file)
	name=$(basename $file)
	md5file="$dir/.$name.md5"

	if [ ! -f $file ]; then
		echo "No file: $file" >&2
		exit 3
	fi
	md5old=$(cat $md5file 2> /dev/null)	# jak brak pliku, to sypie bledem
	md5new=$(md5sum $file | cut -d\  -f1)

	if [[ "$md5old" != "$md5new" ]]; then
		echo -n "$md5new" > $md5file
		code=0
	fi
done
exit $code
