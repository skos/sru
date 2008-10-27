#!/bin/bash
dir='/opt/skos/sru-walet'
tmp="/tmp/sru-walet.tmp"
changed="$(dirname $0)/changed.sh"
walet="$(dirname $0)/walet2sql.py"
db='skos'

$walet > $tmp && mv $tmp "$dir/walet.sql" && \
$changed "$dir/walet.sql" && psql "$db" -f "$dir/walet.sql" -q
