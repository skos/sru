#!/bin/bash


if [[ -z $1 ]]; then
	function escape() {
		echo ${*//\'/\\\'}
	}
	read line
	echo -ne "\t"
	echo -n "'$(escape $MSGEXEC_MSGID)' => '$(escape $line)',"
	echo -ne "\t"
	echo "// $MSGEXEC_LOCATION"
	exit;
fi
echo '<?'
echo '// NIE MODYFIKUJ TEGO PLIKU'
echo '$dict = array('
for file in `seq $#`; do
	msgexec -i "$1" "$0"
	shift
done
echo ');'
