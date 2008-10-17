#!/bin/bash
tmp="/tmp/sru-dhcp.tmp"
dir='/opt/skos/dhcp'

wget -q -O $tmp http://sru.ds.pg.gda.pl/api/dhcp/stud && mv $tmp "$dir/studs.inc"
wget -q -O $tmp http://sru.ds.pg.gda.pl/api/dhcp/org && mv $tmp "$dir/org.inc"
wget -q -O $tmp http://sru.ds.pg.gda.pl/api/dhcp/adm && mv $tmp "$dir/adm.inc"
/etc/init.d/dhcpd restart
