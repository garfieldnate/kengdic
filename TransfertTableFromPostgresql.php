<?php

/* [Larpoux 01/2019]
This script is for migrating Postgresql tables to Sqlite.
This transfert the table directly, without using an export file format.
It was necessary because I had several problems during the export/import process because of escape charaters handled differently in the two databases.
\\\\ for example or \\''
*/
	$host        = "host=localhost";
	$port        = "port=5432";
	$dbname      = "dbname=postgres";
    	$credentials = "user=postgres password=xxxxxx";

    	$db = pg_connect( "$host $port $dbname $credentials"  );
	if($db) 
	{
		echo "Opened source database successfully\n";
	}
	else 
	{
		echo "Error : Unable to open source database\n";
	    die();
	} 

	try
	{
		$pdo = new PDO('sqlite:kengdic.db');
		echo "Opened object database successfully\n";
	} catch(Exception $e) 
	{   echo "Error : Unable to open object database\n : ".$e->getMessage();
	    die();
	}


	$stmt = $pdo->prepare
		("INSERT INTO korean_english (wordid,word,syn,def,posn,pos,submitter,doe,wordsize,hanja,wordid2,extradata) 
		   VALUES (
					:wordid, :word, :syn, :def, :posn, :pos, :submitter, :doe, :wordsize, :hanja, :wordid2, :extradata
		       	  )
		");

$sql =<<<EOF
    SELECT * from korean_english5;
EOF;

	$ret = pg_query($db, $sql);
	if(!$ret) 
	{
		echo pg_last_error($db);
		exit;
	} 
	
	$total = 0;
	while($row = pg_fetch_row($ret)) 
	{
		$total++;
		$result = $stmt -> execute( $row );
	}
	echo "Operation done successfully : total = ".$total." tuples\n";


	pg_close($db);
?>
