#!/usr/bin/env python

import sys
import urllib

tablename = 'users_walet'

dorms = {
'1':'1',
'2':'2',
'3':'3',
'4':'4',
'5':'5',
'6':'7',
'7':'8',
'8':'9',
'9':'10',
'10':'11',
'11':'12',
}

def dormitory(no):
	file = urllib.urlopen('http://walet.pg.gda.pl/sru.php?ds=%s' % (no))
	dorm = dorms[no]

	for line in file.readlines():
		(hash, room) = line.strip().split("\t")
		print "%s\t%s\t%s" % (hash, dorm, room)

try:
	print "truncate %s;" % (tablename)
	print "copy %s (hash, dorm, room) from stdin;" % (tablename)
	for no in dorms.keys():
		dormitory(no)
	print "\."
except:
	sys.exit(1)

