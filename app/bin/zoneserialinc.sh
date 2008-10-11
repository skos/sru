#!/bin/bash

zsu="$(dirname $0)/zsu"

function help() {
	cat << EOH
$(basename $0) zone_file check_file_1 [check_file_2] ...
EOH
}

if [[ "$#" -lt 2 ]]; then
	help;
fi

errors=0

serialFile="$1"
serialFileCheck="$(dirname $serialFile)/.$(basename $serialFile).increment"
if [ ! -f $serialFile ]; then
	echo "No file: $serialFile" >&2
	exit -1
fi
shift;

for file in "$@"; do
	dir=$(dirname $file)
	name=$(basename $file)
	md5file="$dir/.$name.md5"

	if [ ! -f $file ]; then
		echo "No file: $file" >&2
		errors=$(( $errors + 1 ))
		continue
	fi
	md5old=$(cat $md5file 2> /dev/null)	# jak brak pliku, to sypie bledem
	md5new=$(md5sum $file | cut -d\  -f1)

	if [[ "$md5old" != "$md5new" ]]; then
		touch $serialFileCheck
		echo -n "$md5new" > $md5file
	fi
done

if [ -f $serialFileCheck ]; then
	$zsu -f $serialFile && rm $serialFileCheck
	errors=$(( $errors + $? ))
fi

exit $errors
