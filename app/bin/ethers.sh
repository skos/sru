#!/bin/bash
dir='/opt/skos/ethers'
tmp="/tmp/sru-ethers.tmp"

wget -q --no-check-certificate -O $tmp https://sru.ds.pg.gda.pl/api/ethers && mv $tmp "$dir/ethers"
