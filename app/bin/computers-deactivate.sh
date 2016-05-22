#!/bin/bash

BASE=$(dirname "$0")
LOGFILE="$BASE/../var/log/cron.log"
PIDFILE="$BASE/../var/run/cron_deactivate.pid"

. $BASE/api.sh

if [[ -e "$PIDFILE" ]]; then
	log "Other instance is running"
	echo "Other instance is running"
	exit 1
fi

echo "$$" > "$PIDFILE"

for host in $(get 'computers/outdated'); do
	del "computer/${host}" && log "${host}" || log "${host} ERROR"
done

for alias in $(get 'computers/outdatedaliases'); do
	del "computer/deldnsentry/${alias}" && log "alias ${alias}" || log "alias ${alias} ERROR"
done

rm "$PIDFILE"
