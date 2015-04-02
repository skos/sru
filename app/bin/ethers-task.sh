#!/bin/bash
changed="$(dirname $0)/changed.sh"

url='rsync://arp-ds.task.gda.pl/PG-arp'
dir='/usr/share/sru/opt/ethers/'
tmp="/tmp/sru-ethers.tmp"

echo -n '' > "${dir}/.lock"
echo -n '' > "${dir}/.nolock"
wget -q --no-check-certificate -O $tmp http://chinook.srv/api/ethers && mv $tmp "$dir/ethers" && \
$changed "$dir/ethers" && rsync --inplace "${dir}/.lock" "$dir/ethers" "${dir}/.nolock" "$url" && \
echo "$(date) Tablica ARP zostala wyslana"
