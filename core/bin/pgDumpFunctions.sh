#!/bin/bash
#
# parametry:
#	baza danych
# 	uzytkownik bazy
#	nazwy tabel do zdumpowania (rozdzielone spacjami)

user="${2}"
base="${1}"

# "kasujemy" dwa pierwsze parametry
shift
shift

funcs=''
for param in "${@}"; do
	if [[ -z "${funcs}" ]]; then
		funcs="${param}("
	else
		funcs="${funcs}\\|${param}("
	fi
done

pg_dump -E UTF-8 -Fc -Oxc -f ${$}.dump -U "${user}" "${base}"

pg_restore -l ${$}.dump | grep FUNCTION > ${$}.funcs-pre

if [[ -z "${funcs}" ]]; then
	cp ${$}.funcs-pre ${$}.funcs
else
	grep "${funcs}" ${$}.funcs-pre > ${$}.funcs
fi

pg_restore -OxL ${$}.funcs ${$}.dump

rm ${$}.dump ${$}.funcs-pre ${$}.funcs
