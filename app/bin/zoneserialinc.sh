#!/bin/bash

zsu="$(dirname $0)/zsu"
changed="$(dirname $0)/changed.sh"

function help() {
	cat << EOH
$(basename $0) zone_file check_file_1 [check_file_2] ...
EOH
}

if [[ "$#" -lt 2 ]]; then
	help;
fi

serialFile="$1"
if [ ! -f $serialFile ]; then
	echo "No file: $serialFile" >&2
	exit -1
fi
shift;

$changed $@ && $zsu -f $serialFile

exit $errors
