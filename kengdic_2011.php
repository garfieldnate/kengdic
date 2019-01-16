<?php
include("_common.php");
global $from_lang;

$save_test_file = "kengdic.sql";
$fh = fopen('kengdic.sql', 'a');
$lines = file($test_file);
#
$offset_into_m_check_word = 'blank_place_holder' ;
$sql = "select wordid, word, syn, def, posn, pos, submitter, doe, wordsize, hanja from korean_english where word is NOT NULL AND word != '' order by word offset 120000  "; // m_ is ordered by word
$Res = queryAll($db, $sql);
$other = '';
// information to be in dump file 
//
// information preceded by asterisk is a real column
//
//  * wordid    | integer                     | default nextval('korean_english_wordid_seq'::regclass)
//  * word      | character varying(130)      | 
//  * syn       | character varying(190)      |  // synonym: turned into a placeholder for a strange note about the word
// sublevel
//  * def       | text                        | 
//  * posn      | integer                     | 
//  * pos       | character varying(13)       | 
/// NOTE: I never got around to using part of speech and part of speech number as this dictionary was 
///       in such a primitive stage words were the priority
// e.g. noun, verb, etc.
//  * submitter | character varying(25)       | 
//  * doe       | timestamp without time zone | 
//  * wordsize  | smallint   
// PROCESSING NOTE .... already in DB use as is                  | 
//  * hanja     | character varying           | 
//  PROCESSING NOTE .... strip_tags() on them to clean them of the href link
// * topic_id -- so that you can link to the example file (see other sql file)
// .... function find_topic_from_wordid($wordid,$switch,$from_lang,$level, $debug = 0) {
// * type of entry  (gsso [go sah sung oh],m [most popular 6000 word member],..
// PROCESSING OMITTED: hanja, survival, proverbs, gotcha
// TOODO: add in the grammar entries
// PROCESSING NOTE ....... if ($sub_row['def'] == 'see 6000' || $sub_row['def'] == 'see gsso') {
// * level
// e.g. A, B, C  (D == place names)  [ for most common word entries only ]

for ($abc = 0; $abc < count($Res) ; $abc++ ) {
   $run = $Res[$abc];
   $word = trim($run['word']);
   // get everything if
   // I am simplifing my crazy system, you are not meant to know what Y i have to do this 
   // but you can thank me. 
   if ($run['def'] == 'see 6000' )  { 
        $mode_6000 = 1 ;  // for replacing switch and stuff at end of table
        // need to handle here j
        if ($offset_into_m_check_word != $word ) {
          $offset_into_m = 0 ; // reset the offset part of the query
        }
        $offset_into_m_check_word = $word ;
        $switch = 'm' ;
        $mode_6000_query = "select wordid, word, syn, hanja, def, pos, submitter, doe, wordsize, level,posn from m_korean where word = '" . $word  . "' offset $offset_into_m limit 1";
        $offset_into_m++;  // sometimes we have two same words in the m_korean table
        $tableArr = mod_queryRow($db,$mode_6000_query);
        $topic_wordid = $tableArr['wordid'];
        $topic_id = $topic_wordid + 200000;  //

	// these are the correct ones
        $hanja = $tableArr['hanja'];
        $pos = $tableArr['pos'];
        $def = $tableArr['def'];
        $posn = $tableArr['posn'];  // sublevel, actually used
        $level  = $tableArr['level'];
   } else if ( $run['def'] == 'see gsso') {
	$switch = 'gsso';
        $gsso_query = "select wordid, word, syn, hanja, def, pos, submitter, doe, wordsize, level,posn from gsso_korean where word like '%" . $word  . "%'";
        $tableArr = mod_queryRow($db,$gsso_query);
        $topic_wordid = $tableArr['wordid']; // do we need change this?
        $topic_id = $topic_wordid + 600000;  //

	// these are the correct ones
        $hanja = $tableArr['hanja'];
        $pos = $tableArr['pos'];
        $def = $tableArr['def'];
        $posn = $tableArr['posn'];
        $level  = $tableArr['level'];

   } else { 
	// these are the correct ones
	$switch = '';
        $hanja = $run['hanja'];
        $pos = $run['pos'];
        $def = $run['def'];
        $posn = $run['posn'];
        $level  = '';
	$topic_wordid = $run['wordid']; 
	$topic_id = $run['wordid'];
   } 
   $hanja = str_replace("\n", " ",$hanja); 
   $hanja = str_replace(" ", "",$hanja); 
   $wrstr = $run['wordid'] . '\t' . $word . '\t' . $run[syn] . '\t' ; 
   $wrstr .= addslashes($def) . '\t' . $posn . '\t'. $pos . '\t' . $run[submitter] . '\t' . $run[doe] . '\t' . $run[wordsize] . '\t' ; 
   $wrstr.= strip_tags($hanja)  . '\t';
   $wrstr.= $topic_id ;
   $wrstr.= '\t' . $switch  ;
   $wrstr .= \t' . $level . "\n";  
//   echo $wrstr . "<hr>";
   fwrite($fh, $wrstr);
} 

fclose($fh);

?>
