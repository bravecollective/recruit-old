#!/usr/bin/python
import requests
import re
import operator
from lxml import etree
import sys
import time
import datetime
import MySQLdb

# ---------------------------------------------

online = True

# ---------------------------------------------

db = MySQLdb.connect(host="localhost", user="root", passwd="root", db="recruit")
cur = db.cursor()

if (online):
	req = requests.get('https://api.eveonline.com//eve/AllianceList.xml.aspx')
	root = etree.fromstring(req.text.encode("utf-8"))
else:
	root = etree.parse(open('AllianceList.xml.aspx','r'))

rows = root.xpath("/eveapi/result/rowset/row")
for row in rows:
	allianceId = int(row.xpath("@allianceID")[0])
	allianceName = str(row.xpath("@name")[0])
	allianceTicker = str(row.xpath("@shortName")[0])
	foundedAt = str(row.xpath("@startDate")[0])
	foundedAtDT = datetime.datetime.strptime(foundedAt, "%Y-%m-%d %H:%M:%S")
	foundedAtUT = int(time.mktime(foundedAtDT.timetuple()))

	try:
	    cur.execute("INSERT INTO alliance_lookup (allianceId, allianceName, allianceTicker, foundedAt) VALUES (%s, %s, %s, %s);", (allianceId, allianceName, allianceTicker, foundedAtUT))
	    db.commit()
	except MySQLdb.Error, e:
	    pass

	rows2 = row.xpath("rowset/row")
	for row2 in rows2:
	    corpId = int(row2.xpath("@corporationID")[0])
	    joinedAt = str(row2.xpath("@startDate")[0])
	    joinedAtDT = datetime.datetime.strptime(joinedAt, "%Y-%m-%d %H:%M:%S")
	    joinedAtUT = int(time.mktime(joinedAtDT.timetuple()))

	    try:
		cur.execute("INSERT INTO alliance_history (allianceId, corporationId, joinedAt) VALUES (%s, %s, %s);", (allianceId, corpId, joinedAtUT))
		db.commit()
	    except MySQLdb.Error, e:
		pass
