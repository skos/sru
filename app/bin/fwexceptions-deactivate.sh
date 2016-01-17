#!/bin/bash

BASE=$(dirname "$0")
LOGFILE="$BASE/../var/log/cron.log"
PIDFILE="$BASE/../var/run/cron_deactivate_fwexceptions.pid"

. $BASE/api.sh

if [[ -e "$PIDFILE" ]]; then
	log "Other instance is running"
	echo "Other instance is running"
	exit 1
fi

echo "$$" > "$PIDFILE"

for ex in $(get 'firewallexceptions/outdated'); do
	del "firewallexceptions/${ex}" && log "${ex}" || log "${ex} ERROR"
done

rm "$PIDFILE"
