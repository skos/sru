#!/bin/bash
increment="$(dirname $0)/zoneserialinc.sh"
tmp="/tmp/sru-api.tmp"
base="/opt/skos/dns"
dir="$base/inc"

wget -q -O $tmp http://sru.ds.pg.gda.pl/api/dns/ds && mv $tmp "$dir/ds.inc"
$increment "$base/M/ds.zone" "$dir/ds.inc"
wget -q -O $tmp http://sru.ds.pg.gda.pl/api/dns/adm && mv $tmp "$dir/adm.inc"
$increment "$base/M/adm.zone" "$dir/adm.inc"
for i in `seq 207 223`; do
	wget -q -O $tmp "http://sru.ds.pg.gda.pl/api/dns/${i}" && mv $tmp "$dir/${i}.inc"
	$increment "$base/M/${i}.ds" "$dir/${i}.inc"
done
/usr/sbin/rndc reload
