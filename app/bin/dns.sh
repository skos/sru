#!/bin/bash
increment="$(dirname $0)/zoneserialinc.sh"
tmp="/tmp/sru-dns.tmp"
base="/opt/skos/dns"
dir="$base/inc"

changed=0

wget -q --no-check-certificate -O $tmp https://sru.ds.pg.gda.pl/api/dns/ds && mv $tmp "$dir/ds.inc"
$increment "$base/M/pl.gda.pg.ds" "$dir/ds.inc" && changed=1
wget -q --no-check-certificate -O $tmp https://sru.ds.pg.gda.pl/api/dns/adm && mv $tmp "$dir/adm.inc"
$increment "$base/M/pl.gda.pg.ds.adm" "$dir/adm.inc" && changed=1
for i in `seq 207 223`; do
	wget -q --no-check-certificate -O $tmp "https://sru.ds.pg.gda.pl/api/dns/${i}" && mv $tmp "$dir/${i}.inc"
	$increment "$base/M/153.19.${i}" "$dir/${i}.inc" && changed=1
done
if [[ $changed == "1" ]]; then
	echo -n "$(date) "
	#/usr/sbin/rndc reload
	/etc/init.d/bind9 restart
fi
