# [Larpoux 01/2019]

This is the database as rescued by Garfieldnate five years ago
I just did some cleaning but did not change the data themselves

* Migrate the database to SQLite

* Remove duplicate entries

* Merge the three tables
  * kengdic_2011.tsv
  * kengdic.sql
  * ezcorean_6000.sql

* Try to fill missing fields in one table from fields of other two tables
  * word
  * def
  * syn
  * hanja
  * level

Note : level was a field interesting in table ezcorean that was not present in korean_english table. I added this column that I think can be interesting

* Add a column 'err' for tagging records that cannot be fixed automaticaly
  * 0 = No error dected
  * 1 = This record has a null key for column 'word'
  * 2 = Field 'word' seems bad : several words separated by a space, comma or parenthesis. Latin characters inside the field. Numbers inside the field.

* Export the korean_english table in two export format :
  * csv
  * sql
  Those two export files are supposed to be same and we can use one or the other interchangely.

* Remove old export files wich are now (I hope) obsoloetes.
