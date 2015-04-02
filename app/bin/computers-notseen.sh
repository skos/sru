#!/bin/bash

BASE=$(dirname "$0")
LOGFILE="$BASE/../var/log/cron.log"
PIDFILE="$BASE/../var/run/cron_deactivate_notseen.pid"

. $BASE/api.sh

if [[ -e "$PIDFILE" ]]; then
	log "Other instance is running"
	echo "Other instance is running"
	exit 1
fi

echo "$$" > "$PIDFILE"

for host in $(get 'computers/notseen'); do
	del "computer/${host}" && log "${host}" || log "${host} ERROR"
done

rm "$PIDFILE"
