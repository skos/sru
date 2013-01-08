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

tables=''
for param in "${@}"; do
	tables="${tables} -t ${param}"
done

if [[ -z "${tables}" ]]; then
	pg_dump -E UTF-8 -cOxs -U "${user}" "${base}"
else
	pg_dump -E UTF-8 -cOxs -U "${user}" ${tables} "${base}"
fi
