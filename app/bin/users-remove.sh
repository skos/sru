#!/bin/bash

BASE=$(dirname "$0")
LOGFILE="$BASE/../var/log/cron.log"
PIDFILE="$BASE/../var/run/cron_users_remove.pid"

. $BASE/api.sh

if [[ -e "$PIDFILE" ]]; then
	log "Other instance is running"
	echo "Other instance is running"
	exit 1
fi

echo "$$" > "$PIDFILE"

for id in $(get 'users/toremove'); do
	del "userremove/${id}" && log "${id}" || log "${id} ERROR"
done

rm "$PIDFILE"
