#!/bin/bash

listing=''
dir=$(dirname "$0")
while [ 1 ]; do
	tmp=$(ls -l --full-time ${*})
	if [[ "$listing" != "$tmp" ]]; then
		for file in `ls ${*}`; do
			echo -e "Regeneracja" "$file\t" $(date)
			$dir/generateCss.sh "$file"
		done
	fi
	listing="$tmp"
	sleep 1
done
