#!/bin/env python
import sys
import sqlite3

conn = sqlite3.connect("../var/test.db")
c = conn.cursor()

inFile = open(sys.argv[1], "r")
testId = sys.argv[2]

qoid=-1
aoid=-1
for line in inFile:
    split = line.rstrip('\n').split('\t')
    if split[0] != '' and split[0] != '*':
        print "Question: " + split[0]
        # Does the Question exist?
        c.execute("SELECT oid, Text FROM Question WHERE Text LIKE ?", [split[0]])
        qr = c.fetchall()
        if len(qr) > 0:
            qoid=qr[0][0]
            print "Question exists in database"
        else:
            c.execute("INSERT INTO Question (Text) VALUES (?)", [split[0]])
            conn.commit()
            print "Question inserted into database"
            c.execute("SELECT oid, Text FROM Question WHERE Text like ?", [split[0]])
            qr = c.fetchall()
            qoid=qr[0][0]
        # Is this question associated with this Test?
        c.execute("SELECT Test, Question FROM TestQuestion WHERE Test = ? AND Question = ?", [testId, qoid])
        tar = c.fetchall()
        if len(tar) > 0:
            print "Question already associated with this Test"
        else:
            c.execute("INSERT INTO TestQuestion (Test, Question) VALUES (?, ?)", [testId, qoid])
            conn.commit()
            print "Question associated with Test"
    elif split[1] != '':
        print "\tAnswer: " + split[1]
        # Does the Answer exist?
        c.execute("SELECT oid, Text FROM Answer WHERE Text LIKE ?", [split[1]])
        ar = c.fetchall()
        if len(ar) > 0:
            aoid=ar[0][0]
            print "\tAnswer exists in database"
        else:
            c.execute("INSERT INTO Answer (Text) VALUES (?)", [split[1]])
            conn.commit()
            print "\tAnswer inserted into database"
            c.execute("SELECT oid, Text FROM Answer WHERE Text LIKE ?", [split[1]])
            ar = c.fetchall()
            aoid=ar[0][0]
        # Is this the correct answer?
        if split[0] == '*':
            c.execute("UPDATE Question SET CorrectAnswer = ? WHERE oid = ?", [aoid, qoid])
            conn.commit()
            print "\t\tCorrect Answer"
        # Does this QuestionAnswer exist?
        c.execute("SELECT * FROM QuestionAnswer WHERE Question = ? AND Answer = ?", [qoid, aoid])
        qar=c.fetchall()
        if len(qar) > 0:
            print "\tQuestionAnswer combo exists"
        else:
            print "\tInserting QuestionAnswer combo"
            c.execute("INSERT INTO QuestionAnswer (Question, Answer) VALUES (?, ?)", [qoid, aoid])
            conn.commit()

print "The end"
