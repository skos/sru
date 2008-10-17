#!/bin/bash
dir='/opt/skos/ethers'
tmp="/tmp/sru-ethers.tmp"

wget -q -O $tmp http://sru.ds.pg.gda.pl/api/ethers && mv $tmp "$dir/ethers"
