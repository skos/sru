if [ -z $BASE ]; then
	. api.config
else
	. $BASE/api.config
fi

function log() {
	echo $(date) $(basename "$0") "$1" >> "$LOGFILE"
}

function req() {
	lwp-request -m ${1} -H "Authorization: Basic ${authString}" "${url}/api/${2}"
}

function get() {
	req GET "$1"
}

function del() {
	req DELETE "$1"
}

function put() {
	req PUT "$1"
}
