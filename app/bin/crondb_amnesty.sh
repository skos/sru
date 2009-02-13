#!/bin/bash

BASE=$(dirname "$0")
LOGFILE="$BASE/../var/log/cron.log"
PIDFILE="$BASE/../var/run/cron_amnesty.pid"

. $BASE/crondb.sh

if [[ -e "$PIDFILE" ]]; then
	log "Other instance is running"
	echo "Other instance is running"
	exit 1
fi

echo "$$" > "$PIDFILE"

time0=$(date +%s)

out1=$(query "select remove_bans()")

time1=$(date +%s)

dur1=$(timediff $time1 $time0)

log "remove_bans: ${out1} in ${dur1} sec"

rm "$PIDFILE"
