# CAUTION! This is not to be used as a script
# -------------------------------------------
# This is just several SQL expression that I used during my manual cleaning process
# so that I can COPY/PAST into a SQL shell
# Certainly I will use some of those statements in a future CleaningSsript
#
# Certainly a SQL expert will do much better and much simpler. I am not a SQL expert. 

# Copy a table in a common database
CREATE TABLE korean_english AS SELECT* FROM  korean_english4

# Many problems during JOINs with NULL values
UPDATE korean_english SET word='' WHERE word is NULL;
UPDATE korean_english SET syn='' WHERE syn is NULL;
UPDATE korean_english SET def='' WHERE def is NULL;
UPDATE korean_english SET hanja='' WHERE hanja is NULL;

# Select records who have duplicates on the 4 distinctive columns of the old table korean_english
SELECT c , * FROM  (SELECT COUNT(*) AS c, * FROM korean_english AS t2 GROUP BY word, syn, def, hanja) LEFT JOIN korean_english USING (word, syn, def,hanja) WHERE c > 1;
# Note : this is very interesting because we can find 36 tupples in this table who are duplicated for those four fields.


# Select only duplicates
SELECT * FROM korean_english WHERE rowid NOT IN (SELECT min(rowid) FROM korean_english GROUP BY word, syn, def, hanja);

# Delete duplicates tuples
DELETE FROM korean_english WHERE rowid NOT IN (SELECT min(rowid) FROM korean_english GROUP BY word, syn, def, hanja);

# Merge 2 Relations
INSERT INTO korean_english (wordid,word,syn,def,pos,submitter,doe,wordsize,level,posn,hanja) SELECT * FROM korean_english6;

# Some entries has no Korean word !
SELECT * FROM korean_english WHERE word='';
# I do not know what to do with those records. The definitions look good. There is even an hanja field for one tuple! 
# But I cannot attach those deifinitions to the database without the index 'word'.
# must delete those records
 
# Create a CSV export file
.headers 
.mode csv
.output korean_english.csv
SELECT * FROM korean_english;
.output stdout

# Create a SQL dump
.output korean_english.sql
.dump korean_english
.output stdout

# Replace NULL values if the field is specified inside another duplicate
UPDATE korean_english AS t3 SET def = (SELECT t2.def  FROM korean_english AS t1 JOIN korean_english AS t2 ON t1.word=t2.word AND t1.def = '' AND NOT t2.def = '') WHERE t3.def = ''; 

# Recreate a table from a CSV export
SELECT COUNT(*) FROM korean_english;
DROP TABLE korean_english;
.mode csv
.import korean_english.csv korean_english 
SELECT COUNT(*) FROM korean_english;

INSERT INTO korean_english (wordid,word,syn,def,posn,pos,submitter,doe,wordsize,hanja,wordid2,level) SELECT wordid,word,syn,def,posn,pos,submitter,doe,wordsize,hanja,wordid2,1 FROM korean_english5 AS t1 WHERE t1.word='';

# Update NULL values by finding from duplicates
# Caution : This is very, very long! I really miss Postgresql (it was possible to use several tables and JOINs during UPDATE and DELETE)
UPDATE korean_english  SET def = (SELECT korean_english7.def FROM korean_english7 WHERE korean_english.word = korean_english7.word AND NOT korean_english7.def = '') WHERE korean_english.def = '';

# Find records missing
SELECT * FROM korean_english4 AS t1 LEFT JOIN korean_english AS t2 ON t1.word = t2.word AND t1.def = t2.def WHERE t2.word IS NULL;
