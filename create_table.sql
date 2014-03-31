CREATE DATABASE kengdic;
\connect kengdic;

CREATE TABLE korean_english (
		id SERIAL PRIMARY KEY,
    wordid integer,
    word character varying(130),
    syn character varying(190),
    def text,
    posn integer,
    pos character varying(13),
    submitter character varying(25),
    doe timestamp without time zone,
    wordsize smallint,
    hanja character varying,
		wordid2 integer,
		extradata character varying
);


\COPY korean_english(wordid, word, syn, def, posn, pos, submitter, doe, wordsize, hanja, wordid2, extradata) FROM 'kengdic_2011_cleaned.tsv' NULL 'NULL';
