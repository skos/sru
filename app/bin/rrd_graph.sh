#!/bin/bash
data=''
dir=''

# @param mac
# @param tag
function generate() {
		echo -n "${1} "
		rrdtool graph "${dir}/${1}${3}${4}.png" \
		"DEF:bytes=${data}/${1}.rrd:tcpbytes:LAST" \
		"DEF:bytesavg=${data}/${1}.rrd:tcpbytes:AVERAGE" \
		'CDEF:bytesNorm=bytes,60,/' \
		'CDEF:bytesavgNorm=bytesavg,60,/' \
		'AREA:bytesavgNorm#FFDDDD:average (10min)' \
		'LINE1:bytesNorm#FF0000:transfer' \
		-b 1024 -w 863 -h 350 -e "${3} ${4}" -s end-3h  -v B/s -t "${2} - 3h" -l 0

		echo -n "${1} day"
		rrdtool graph "${dir}/${1}${3}${4}.day.png" \
		"DEF:bytesavg=${data}/${1}.rrd:tcpbytes:AVERAGE" \
		'CDEF:bytesavgNorm=bytesavg,60,/' \
		'LINE1:bytesavgNorm#FF0000:transfer' \
		-b 1024 -w 863 -h 350 -e "${3} ${4}" -s end-1d -v B/s -t "${2} - 24h" -l 0

                echo -n "${1} week"
		rrdtool graph "${dir}/${1}${3}${4}.week.png" \
		"DEF:bytesavg=${data}/${1}.rrd:tcpbytes:AVERAGE" \
                'CDEF:bytesavgNorm=bytesavg,60,/' \
                'LINE1:bytesavgNorm#FF0000:transfer' \
                -b 1024 -E -w 863 -h 350-e "${3} ${4}" -s end-7d -v B/s -t "${2} - tydzień" -l 0

                echo -n "${1} month"
		rrdtool graph "${dir}/${1}${3}${4}.month.png" \
		"DEF:bytesavg=${data}/${1}.rrd:tcpbytes:AVERAGE" \
                'CDEF:bytesavgNorm=bytesavg,60,/' \
                'AREA:bytesavgNorm#FF6666:transfer' \
                -b 1024 -w 863 -h 350 -e "${3} ${4}" -s end-30d -v B/s -t "${2} - miesiąc" -l 0
                
		echo -n "${1} year"
		rrdtool graph "${dir}/${1}${3}${4}.year.png" \
		"DEF:bytesavg=${data}/${1}.rrd:tcpbytes:AVERAGE" \
                'CDEF:bytesavgNorm=bytesavg,60,/' \
                'AREA:bytesavgNorm#FF6666:transfer' \
                -b 1024 -w 863 -h 350 -e "${3} ${4}" -s end-1y -v B/s -t "${2} - rok" -l 0
}

if [[ -z ${1} || -z ${2} ]]; then
	echo 'Brak MACa lub nazwy hosta.'
	exit;
fi

if [[ ! -z ${2} && ! -z ${3} ]]; then
	generate ${1} ${2} ${3} ${4}
	exit;
else
        generate ${1} ${2}
        exit;
fi
