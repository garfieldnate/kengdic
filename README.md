
kengdic is a large Korean/English dictionary database created by Joe Speigle. It was originally hosted at ezkorean.com, and I have posted it here (https://github.com/garfieldnate/kengdic) because it is no longer available anywhere else. It is released under [MPL 2.0](http://www.mozilla.org/MPL/2.0/).

* `kengdic.sql` is a PostgreSQL database dump of a the Korean/English dictionary. It assumes the existence of a `modpgwebuser` schema, as well as a `korean_english_wordid_seq` sequence. It contains:

    * Korean/English spelling
    * Hanja spellings, comma-separated
    * English definition
    * part of speech
    * source of the entry

TODOs for each file:

* `kengdic.sql`:

    * add extra statements necessary for the file to be importable without any editing or specially prepared database
    * fix entries where the entries are HTML containing both hangeul and hanja in text body
    * normalize Hanja (have relation table and hanja table separate instead of comma-separating them)
    * find the difference between `pos` and `posn`
    * find which numbers indicate which part of speech
    * add comments for all columns
    * replace 'see 6000' in the definitions with the definition from the 6000 list from ezkorean.com
    * add primary key (I'm pretty sure that would be good)
    * export to sqlite

Information may be incomplete, as I am still exploring and documenting the contents of this repository. Any contributions to information about this content would be much appreciated.

