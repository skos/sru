#!/bin/bash
dir=$(dirname "$0")
while [ ! -z "${1}" ]; do
	fileCss=$(echo "${1}" | sed 's/\.sass$/.css/')
	fileInc=$(echo "${1}" | sed 's/\.sass$/.inc/')
	if [ -r "${fileInc}" ]; then
		sed 's/^/@import url("/' "${fileInc}" | sed 's/$/");/' > "${fileCss}"
	else
		echo '' > "${fileCss}"
	fi
	
	sed 's/\t/  /g' "${1}" | $dir/../../opt/haml/bin/sass --stdin -t compact >> "${fileCss}"
	shift;
done
