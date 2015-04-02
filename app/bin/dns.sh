#!/bin/bash
increment="$(dirname $0)/zoneserialinc.sh"
tmp="/tmp/sru-dns.tmp"
base="/etc/bind"
dir="$base/inc"
link="http://chinook.srv/api/dns"

changed=0

wget -q --no-check-certificate -O $tmp $link/ds && mv $tmp "$dir/ds.inc"
$increment "$base/db.ds.pg.gda.pl" "$dir/ds.inc" && changed=1
wget -q --no-check-certificate -O $tmp $link/adm && mv $tmp "$dir/adm.inc"
$increment "$base/db.adm.ds.pg.gda.pl" "$dir/adm.inc" && changed=1
for i in `seq 207 223`; do
	wget -q --no-check-certificate -O $tmp "$link/${i}" && mv $tmp "$dir/${i}.inc"
	$increment "$base/db.153.19.${i}" "$dir/${i}.inc" && changed=1
done
if [[ $changed == "1" ]]; then
	echo -n "$(date) "
	#/usr/sbin/rndc reload
	/etc/init.d/bind9 reload
fi
