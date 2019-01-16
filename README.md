kengdic is a large Korean/English dictionary database created by Joe Speigle. It was originally hosted at ezkorean.com, and I have posted it here (https://github.com/garfieldnate/kengdic) because it is no longer available anywhere else. It is released under [MPL 2.0](http://www.mozilla.org/MPL/2.0/).

* `kengdic.sql` is a PostgreSQL database dump of a the Korean/English dictionary as of 2009. It assumes the existence of a `modpgwebuser` schema, as well as a `korean_english_wordid_seq` sequence. It contains one table, `korean_english` with 143,795 rows. There is no primary key, but indexes are created on `doe`, `wordid`, and `word`. This table contains the following information:

    * Korean/English spelling
    * Hanja spellings, comma-separated
    * English definition
    * part of speech
    * source of the entry

* `kengdic_2011.tsv` is a tab-separated file containing a database dump of the same Korean/English dictionary, but instead of being a SQL file it is simply a tab-separated file containing all of the data. It contains 133,876 rows. As this is probably cleaner, newer, better data than `kengdic.sql`, it may not be worth cleaning up that file at all.

* `create_kengdic.sql` creates the kengdic database and sources the data in `kengdic_2011.tsv`. To run it:
    * Make sure postgres is running on your machine and run this command in the terminal: `psql < create_table.sql`
        * You may need to provide a user with the `-U` switch, like so: `psql -U username < create_table.sql`
    * This should create a database called 'kengdic' with one table called 'korean_english'
    * Run 'psql kengdic' and make sure everything imported correctly: "SELECT COUNT( * ) FROM korean_english;" should tell you there are 133,876 rows in the table.

* `ezcorean_6000.sql` is a database dump of 6000 common Korean words, along
with the definitions and hanja.

TODOs for each file:

* `kengdic.sql`:

    * fix entries where the entries are HTML containing both hangeul and hanja in text body
    * normalize Hanja (have relation table and hanja table separate instead of comma-separating them)
    * find the difference between `pos` and `posn`
    * find which numbers indicate which part of speech
    * add comments for all columns and tables
    * add primary key (I'm pretty sure that would be good; there are already indices, though)

* `kengdic_2011.tsv`:

    * Do something about entries with no definition

Information may be incomplete, as we are still exploring and documenting the contents of this repository. Any contributions to information about this content would be much appreciated.

------------

[larpoux: 01/2019]

(See kengdic\ V1.0\CHANGES.md)

* **NORMAL form**
  The major problems with this database are that its schema is not in a NORMAL form as defined by Codd in 1974.Three fields ('syn' 'def' and 'hanja') may contain several informations separated by a comma or semi-colon.

* **Superfluos columns** :
  'korean_english' stores informations that are irrelevant for a dictionary : wordid, wordid2, posn, pos, wordsize, extradata. These fields are probably used for an implementation dependant program. If an application really need those fields, it must use another table probably in another database.

* **Gramatical form** :
  A word can be used as a verb, an adjectiv or as a noun. I do not think realistic to have three entries in the the dictionary. But this information could be important for the translation. This point need to be addressed later.

* **Homonymy** :
  The word 'sheet', for example, may mean "bed sheet" or "sheet of paper" in English. This has nothing to do with Korean. This is an english specificity. Those problems are very common in french too. I guess that it is the same in Korean.
  I think the simpler for now is to stick on just one table, and put several tupples (rows), one for each meaning. The consequence is that this Relation will not be a Relation "One to One" but a Relation "One to Many" : Field 'word' cannot be a Key.

  A major problem is that now, there is no differences beetween a Word having two similars definitions or having an homonym.

* **Hanja** :
  Homonymy is crucial in Korean. Modern spelling is phonetic. But the traditional Hanja spelling contains informations about the meaning of the word. The consequence is that a simple Korean word has many hanja forms depending on the meaning of the word. I need to investigate further to know if an Hanja word can have several modern transcripting : I have no notion of Korean language.

* **Synonymy** :
  Relation 'korean_english' contains just one column to store every synonym of a word. This is not correct for a NORMAL relation database as specified by Codd.
  But one problem is that Synonymy is not really an 'equivalence relation'.

  * *Reflexivity* : A word is always synonym of itself.
  * *Commutativity* : If a word A is synonym of word B, B is not realy synonym of A.
  * *Transitivity* : If a word A is synonym of B, and B is synonym of word C, A is just      loosely synonym of C.
    To keep things simple, I suggest to pretend that synonymy is an 'Equivalence Relation'.
    We will have a Relation "Many to Many".

* **Key field**
  Field 'word' contains sometimes several Korean words. It contains even sometimes English. This is not good for a field who is supposed to be an index in the future. I cannot do anything right now. I need first to learn Korean :-D .

* **Duplicates def** :
  There are many different definitions of a given Korean 'word' in table 'Korean_english' . It is difficult to clean this field automatically but very often the definitions just differ by minor differences.

* **Transposed dictionary** :
  Field 'def' contains some-times one or several translations to an english word, and sometimes a complete definition in english. If we want to be able to build a transposed dictionary (English to Korean) we will need to have two differents table for 'def'.