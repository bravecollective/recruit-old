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

if (len(sys.argv) != 3):
    print sys.argv[0] + " <keyID> <vCode>"
    sys.exit(1)

api_keyid = sys.argv[1]
api_vcode = sys.argv[2]

# ---------------------------------------------

db = MySQLdb.connect(host="localhost", user="root", passwd="root", db="recruit")
cur = db.cursor()
now = int(time.time())

notificationIdAdded = []
charIdAdded = []
charSeen = []
corpAdded = []

def retrieveNotifications():
    if (online):
	req = requests.get('https://api.eveonline.com/char/Notifications.xml.aspx?keyID=' + api_keyid + "&vCode=" + api_vcode)
	root = etree.fromstring(req.text.encode("utf-8"))
    else:
	root = etree.parse(open('Notifications.xml.aspx','r'))

    rows = root.xpath("/eveapi/result/rowset/row")
    for row in rows:
	reason = int(row.xpath("@typeID")[0])
	if reason not in [16, 17, 18, 21, 128, 129, 130]:
	    continue

	nId = int(row.xpath("@notificationID")[0])
	charId = int(row.xpath("@senderID")[0])
	charName = str(row.xpath("@senderName")[0])
	issuedAt = str(row.xpath("@sentDate")[0])
	issuedAtDT = datetime.datetime.strptime(issuedAt, "%Y-%m-%d %H:%M:%S")
	issuedAtUT = int(time.mktime(issuedAtDT.timetuple()))

	if charName:
	    charSeen.append([str(charId), str(charName)])

	try:
	    cur.execute("INSERT INTO application_history (notificationId, charId, reason, issuedAt) VALUES (%s, %s, %s, %s);", (nId, charId, reason, issuedAtUT))
	    db.commit()
	    notificationIdAdded.append(str(nId))
	    charIdAdded.append(str(charId))
	except MySQLdb.Error, e:
	    pass

def retrieveNotificationDetails(nIds):
    global api_keyid, api_vcode, db, cur

    if (online):
	req = requests.get('https://api.eveonline.com/char/NotificationTexts.xml.aspx?keyID=' + api_keyid + "&vCode=" + api_vcode + "&IDs=" + ",".join(nIds))
	root = etree.fromstring(req.text.encode("utf-8"))
    else:
	root = etree.parse(open('NotificationTexts.xml.aspx','r'))

    rows = root.xpath("/eveapi/result/rowset/row")
    for row in rows:
	nId = int(row.xpath("@notificationID")[0])
	text = str(row.text)
	text = re.sub('\n', ' ', text)
	text = re.sub('.*applicationText: ', '', text)
	text = re.sub('charID: .*', '', text)
	text = re.sub('^\'', '', text)
	text = re.sub('\'$', '', text)
	text = re.sub('\' $', '', text)
	text = re.sub('<[^<]+?>', ' ', text)
	text = re.sub('\ + ', ' ', text)

	if text != "":
	    try:
		cur.execute("UPDATE application_history SET text = %s WHERE notificationId = %s", (text, nId))
		db.commit()
	    except MySQLdb.Error, e:
		pass

def updateChars(chars):
    global db, cur
    for char in chars:
	try:
	    cur.execute("INSERT INTO character_lookup (charId, charName) VALUES (%s, %s)", (char[0], char[1]))
	    db.commit()
	except MySQLdb.Error, e:
	    try:
		cur.execute("UPDATE character_lookup SET charName = %s WHERE charId = %s", (char[1], char[0]))
		db.commit()
	    except MySQLdb.Error, e:
		pass

def retrieveEmploymentHistory(cIds):
    global api_keyid, api_vcode, db, cur

    cIds = list(set(cIds))
    for charId in cIds:
	if (online):
	    req = requests.get('https://api.eveonline.com/EVE/CharacterInfo.xml.aspx?characterID=' + charId)
	    root = etree.fromstring(req.text.encode("utf-8"))
	else:
	    root = etree.parse(open('NotificationTexts.xml.aspx?characterID=' + charId,'r'))

	rows = root.xpath("/eveapi/result/rowset/row")
	for row in rows:
	    rId = int(row.xpath("@recordID")[0])
	    corpId = int(row.xpath("@corporationID")[0])
	    since = str(row.xpath("@startDate")[0])
	    sinceDT = datetime.datetime.strptime(since, "%Y-%m-%d %H:%M:%S")
	    sinceUT = int(time.mktime(sinceDT.timetuple()))

	    try:
		cur.execute("INSERT INTO employment_history (recordId, charId, corporationId, since) VALUES (%s, %s, %s, %s);", (rId, charId, corpId, sinceUT))
		db.commit()
		corpAdded.append(corpId)
	    except MySQLdb.Error, e:
		pass

def retrieveCorporation(cIds):
    global db, cur

    cIds = list(set(cIds))
    for corpId in cIds:
	if (online):
	    req = requests.get('https://api.eveonline.com/corp/CorporationSheet.xml.aspx?corporationID=' + str(corpId))
	    root = etree.fromstring(req.text.encode("utf-8"))
	else:
	    root = etree.parse(open('CorporationSheet.xml.aspx?characterID=' + str(corpId),'r'))

	rows = root.xpath("/eveapi/result")
	for row in rows:
	    corpName = str(row.xpath("corporationName[text()]")[0].text)
	    corpTicker = str(row.xpath("ticker[text()]")[0].text)

	    try:
		cur.execute("INSERT INTO corporation_lookup (corporationId, corporationName, corporationTicker) VALUES (%s, %s, %s);", (corpId, corpName, corpTicker))
		db.commit()
	    except MySQLdb.Error, e:
		pass

retrieveNotifications()

if notificationIdAdded:
    retrieveNotificationDetails(notificationIdAdded)

if charSeen:
    updateChars(charSeen)

if charIdAdded:
    retrieveEmploymentHistory(charIdAdded)

if corpAdded:
    retrieveCorporation(corpAdded)

