#!/bin/bash
url='rsync://arp-ds.task.gda.pl/PG-arp'
dir='/opt/skos/ethers'
tmp="/tmp/sru-ethers.tmp"

echo -n '' > "${dir}/.lock"
echo -n '' > "${dir}/.nolock"
wget -q -O $tmp http://sru.ds.pg.gda.pl/api/ethers && mv $tmp "$dir/ethers" && \
rsync "${dir}/.lock" "$dir/ethers" "${dir}/.nolock" "$url"
