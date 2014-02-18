kengdic is a large Korean/English dictionary database created by Joe Speigle. It was originally hosted at ezkorean.com, and I have posted it here (https://github.com/garfieldnate/kengdic) because it is no longer available anywhere else. It is released under [MPL 2.0](http://www.mozilla.org/MPL/2.0/).

* `kengdic.sql` is a PostgreSQL database dump of a the Korean/English dictionary as of 2009. It assumes the existence of a `modpgwebuser` schema, as well as a `korean_english_wordid_seq` sequence. It contains one table, `korean_english` with 143,795 rows. There is no primary key, but indexes are created on `doe`, `wordid`, and `word`. This table contains the following information:

    * Korean/English spelling
    * Hanja spellings, comma-separated
    * English definition
    * part of speech
    * source of the entry

* `kengdic_2011.sql` is a database dump of the same Korean/English dictionary, but instead of being a SQL file it is simply a tab-separated file containing all of the data. It contains 134,223 rows. As this is probably cleaner, newer, better data than `kengdic.sql`, it may not be worth cleaning up that file at all.

TODOs for each file:

* `kengdic.sql`:

    * fix entries where the entries are HTML containing both hangeul and hanja in text body
    * normalize Hanja (have relation table and hanja table separate instead of comma-separating them)
    * find the difference between `pos` and `posn`
    * find which numbers indicate which part of speech
    * add comments for all columns and tables
    * replace 'see 6000' in the definitions with the definition from the 6000 list from ezkorean.com
    * add primary key (I'm pretty sure that would be good; there are already indices, though)
    * export to sqlite

`kengdic_2011.sql`:

    * figure out how to load it into a database and use it instead of `kengdic.sql`, since it's newer.

Information may be incomplete, as I am still exploring and documenting the contents of this repository. Any contributions to information about this content would be much appreciated.
