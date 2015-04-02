#!/bin/bash

BASE=$(dirname "$0")
LOGFILE="$BASE/../var/log/cron.log"
PIDFILE="$BASE/../var/run/cron_admins_deactivate.pid"

. $BASE/api.sh

if [[ -e "$PIDFILE" ]]; then
	log "Other instance is running"
	echo "Other instance is running"
	exit 1
fi

echo "$$" > "$PIDFILE"

del "admins/delete"

rm "$PIDFILE"
