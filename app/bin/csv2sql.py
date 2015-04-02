#!/usr/bin/env python

import sys
import string
import unicodedata
import md5

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

try:
	infile = sys.argv[1]
except:
	print "Podaj nazwe pliku csv"
	sys.exit(1)

def normalizeName(txt):
	return txt.lower().strip()
	"""
	txt = txt.lower().strip()
	if unicode.isalnum(txt):
		return txt
	txt2 = []
	for l in txt:
		if unicode.isalnum(l):
			txt2.append(l)
		else:
			txt2.append('.')
	return ''.join(txt2)
	"""

def normalizeRoom(txt):
	txt = txt.upper().strip().lstrip('0')
	txt = string.replace(txt, ' ', '')
	return txt

inf = open(infile, 'r')

print "truncate %s;" % (tablename)
print "copy %s (hash, dorm, room) from stdin;" % (tablename)
for line in inf.readlines():
	[tmp,ds,nazwa,pokoj,tmp] = line.decode('UTF-8').split(',')
	tmp = normalizeName(nazwa.strip('"'))
	print tmp.encode('UTF-8')
	tmp = md5.md5(tmp.encode('UTF-8')).hexdigest()
	print "%s\t%s\t%s" % (tmp, dorms[ds], normalizeRoom(pokoj.strip('"')).encode('UTF-8'))
print "\."
