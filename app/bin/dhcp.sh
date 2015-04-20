#!/bin/bash
tmp="/tmp/sru-dhcp.tmp"
dir='/srv/skos/dhcp'
link="http://chinook.srv/api/dhcp"
changed="$(dirname $0)/changed.sh"

wget -q --no-check-certificate -O $tmp $link/stud && mv $tmp "$dir/studs.inc"
wget -q --no-check-certificate -O $tmp $link/org && mv $tmp "$dir/org.inc"
wget -q --no-check-certificate -O $tmp $link/adm && mv $tmp "$dir/adm.inc"
wget -q --no-check-certificate -O $tmp $link/serv && mv $tmp "$dir/serv.inc"


$changed "$dir/studs.inc" "$dir/org.inc" "$dir/adm.inc" "$dir/serv.inc" && /etc/init.d/isc-dhcp-server restart
