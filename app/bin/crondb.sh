. $BASE/crondb.config

function log() {
	echo $(date) $(basename "$0") "$1" >> "$LOGFILE"
}

function query() {
	out=$(echo "\t\a
$1" | $PSQL | tail -n1)
	echo "$out"
}

function timediff() {
	echo $(( $1 - $2 ))
}
