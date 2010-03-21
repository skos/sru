#!/bin/bash
tmp="/tmp/sru-dhcp.tmp"
dir='/opt/skos/dhcp'
changed="$(dirname $0)/changed.sh"

wget -q --no-check-certificate -O $tmp https://sru.ds.pg.gda.pl/api/dhcp/stud && mv $tmp "$dir/studs.inc"
wget -q --no-check-certificate -O $tmp https://sru.ds.pg.gda.pl/api/dhcp/org && mv $tmp "$dir/org.inc"
wget -q --no-check-certificate -O $tmp https://sru.ds.pg.gda.pl/api/dhcp/adm && mv $tmp "$dir/adm.inc"
wget -q --no-check-certificate -O $tmp https://sru.ds.pg.gda.pl/api/dhcp/srv && mv $tmp "$dir/srv.inc"

$changed "$dir/studs.inc" "$dir/org.inc" "$dir/adm.inc" "$dir/srv.inc" && /etc/init.d/dhcpd restart
