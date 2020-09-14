<?php
//require_once './functions/mysql_connection.php';
//  FUNCTIONS
//####################
function cosine_sim($vecstring1, $vecstring2, $sumsq1, $sumsq2) {
	//echo "vecstring1: ".$vecstring1."<br/>";
	$vector1 = explode(" ", $vecstring1);
	$vector2 = explode(" ", $vecstring2);
	$numdims = count($vector1);
	//echo "count: ".$numdims.", ";
	$dotproduct = 0.0;
	$increment = 0.0;
	for ($d = 0; $d < $numdims; $d++) {
		$increment = $vector1[$d] * $vector2[$d];
		$dotproduct = $dotproduct + $increment;
	}
	$magnitude = ($sumsq1 * $sumsq2);
	//echo "magn: ".$magnitude.", ";
	$rawcorrelation = $dotproduct / $magnitude;
	//echo "raw corr: ".$rawcorrelation.". ";
	$correlation = round($rawcorrelation, 4);
	return $correlation;
}

function combine_key($int1, $int2) {
	$newkey = $int1 * 1000000000;
	$newkey = $newkey + $int2;
	return $newkey;
}

function extract_key2($keyint) {
	$key2 = $keyint % 1000000000;
	return $key2;
}

function extract_key1($keyint, $mod) {
	$key0 = $keyint - $mod;
	$key1 = $key0 / 1000000000;
	return $key1;
}

function test_for_presence($fragset, $term, $chunk) {
	$fragtable = "frag250";
	if ($fragset == "ch1000") {
		$fragtable = "frag1000";
	}
	$fragresult = mysql_query("SELECT ctext FROM $fragtable WHERE id=$chunk");
	$fragment = mysql_fetch_row($fragresult);
	$fstring = $fragment[0];
	
	$lc_term = strtolower($term);
	$lc_string = strtolower($fstring);
	
	$ourterm = "/\b".$lc_term."\b/";
	
	if (preg_match($ourterm, $lc_string)) {
		return 1;
	}
	else {
		return 0;
	}
}

//  MAIN PROCEDURE
//####################
// run the search over the correlation matrix

$logfile = "log/chunktermsearch.txt";
$log = fopen($logfile, "w");
if (!$log) {
	echo "log OPEN FAILED.<br/>";
	return;
}
fwrite($log, "open chunktermsearch: ".memory_get_usage()." at ".date("M d g:i:s")."\n");

$mdb = $_GET['mdb'];
$list = $_GET['list'];
$frags = $_GET['frags'];
$scope = $_GET['scope'];
$outf = $_GET['outf'];
$bound = $_GET['bound'];
$qs = $_GET['qs'];

$hash_value = md5($mdb."|".$list."|".$frags."|".$scope."|".$bound."|".$qs);

if($outf == "TDcsv" &&  file_exists("graphs/xy-".$hash_value.".csv")) {
	$downloadpath = "graphs/xy-".$hash_value.".csv";
    echo  "<br/><br/>(Right-click the link and use <em>Save link as...</em> to get a copy of a CSV file for viewing an XY Chart in Excel or another program.)<br/><br/>
				<table>
				<tr><td>XY scatterplot CSV file (cached):</td><td><a href='".$downloadpath."'>Link for download.</a> Give it a new name.</td></tr>
				</table><br/><br/>";
     exit;
}

fwrite($log, "mdb=$mdb, list=$list, frags=$frags, scope=$scope, outf=$outf, bound=$bound, qs=$qs\n");

// open the database
require_once("functions/mysql_connection.php");

//begin setup
$listTable = "doc250_list";
$termlistTable = "term250_list";
$correlationTable0 = "termdoc250_cosines0";
$correlationTable1 = "termdoc250_cosines1";
$correlationTable4 = "termdoc250_cosines4";
if ($frags == "ch1000") {
	$listTable = "doc1000_list";
	$termlistTable = "term1000_list";
	$correlationTable0 = "termdoc1000_cosines0";
	$correlationTable1 = "termdoc1000_cosines1";
	$correlationTable4 = "termdoc1000_cosines4";
}

// put the selected term ids in a memory array and terms in a WHERE IN string unless user chose "ALL"
$selected = array();  // array is used when $qs not "ALL" to detect doc1 and doc2 cases
$chunkindex = array();
$chidx = 0;
$selectedchunks = "";
if ($qs == "ALL") {
	unset($selected);
	print "There's no point in asking for ALL chunks. I'll fix this. Try choosing some chunks.";
	return;
}
else {
	$selectedchunks = str_replace("_", ",", $qs);
	//$selectedchunks = "'".$selectedchunks."'";
	$getselecteddocids = "SELECT id, alch FROM ".$listTable." WHERE id IN (".$selectedchunks.")";
	$selectedrows = mysql_query($getselecteddocids);
	while ($nextid = mysql_fetch_row($selectedrows)) {
		$selected[] = $nextid[0];
		$chunkindex[$nextid[0]] = $chidx;
		$chidx++;
	}
	mysql_free_result($selectedrows);
}
fwrite($log, "selected array loaded: ".memory_get_usage()."\n");
fwrite($log, "count in selected: ".count($selected)."\n");

fwrite($log, "selectedchunks: $selectedchunks\n");

//  going to try using a temporary table called results to organize the work
// create temporary table results
$removeresultstemp = "DROP TEMPORARY TABLE IF EXISTS results";
mysql_query($removepagestemp);
$makeresultstemp = "CREATE TEMPORARY TABLE results (
		correlation TEXT, 
		term INT NOT NULL, 
		doc INT NOT NULL)";
mysql_query($makeresultstemp);

$pairs4 = 0;
$pairs1 = 0;
$pairs0 = 0;
//  load the temporary table
$getcosines4 = "INSERT INTO results (correlation, term, doc)
		SELECT correlation, term, doc FROM $correlationTable4
		WHERE (correlation >= $bound) AND (doc IN ($selectedchunks) )";
mysql_query($getcosines4);
$pairs4 = mysql_affected_rows();
fwrite($log, "getcosines4 query executed: ".memory_get_usage()."\n");
fwrite($log, "cosines4 rows: $pairs4\n");
$pairs5 = mysql_affected_rows();
if ($bound <= 0.4) {
	$getcosines1 = "INSERT INTO results (correlation, term, doc)
			SELECT correlation, term, doc FROM $correlationTable1
			WHERE (correlation >= $bound) AND (doc IN ($selectedchunks) )";
	mysql_query($getcosines1);
	fwrite($log, "getcosines1 query executed: ".memory_get_usage()."\n");
	$pairs1 = mysql_affected_rows();
	fwrite($log, "cosines1 rows: $pairs1\n");
}
if ($bound <= 0.1) {
	$getcosines0 = "INSERT INTO results (correlation, term, doc)
			SELECT correlation, term, doc FROM $correlationTable0
			WHERE (correlation >= $bound) AND (doc IN ($selectedchunks) )";
	mysql_query($getcosines0);
	fwrite($log, "getcosines0 query executed: ".memory_get_usage()."\n");
	$pairs0 = mysql_affected_rows();
}

// load the appropriate term list into memory array for output
$getallterms = "SELECT wordform FROM ".$termlistTable;
$termliststring = "BASE\n";
$termrows = mysql_query($getallterms);
while ($termrow = mysql_fetch_row($termrows)) {
	$termliststring = $termliststring.$termrow[0]."\n";
}
$termlist = explode("\n", $termliststring);
fwrite($log, "termlist array loaded: ".memory_get_usage()."\n");
unset($termliststring);
mysql_free_result($termrows);
fwrite($log, "termliststring and termrows freed: ".memory_get_usage()."\n");
fwrite($log, "termlist count: ".count($termlist)."\n");


// load the chunk list so we can write out titles and files names for displaycorrs.php
$getchunksdata = "SELECT id, ctitle FROM doc250_list";
$displayscript = "javascript:openTDViewer(\"ch250\", \"";
if ($frags == "ch1000") {
	$getchunksdata = "SELECT id, ctitle FROM doc1000_list";
	$displayscript = "javascript:openTDViewer(\"ch1000\", \"";
}
$chunksdata = mysql_query($getchunksdata);

$chunks = array();
$chunks[] = "BASE"; // fills that pesky $chunks[0] member
while ($outchunk = mysql_fetch_array($chunksdata)) {
	$chunks[] = $outchunk;
}
fwrite($log, "chunks name array initialized. memory used: ".memory_get_usage()."\n");
fwrite($log, "chunks array initialized. ".memory_get_usage()."\n");

echo "<br/><br/>";

$output = NULL;
$getoutput = "";
// everything is gathered in results, time to work through them
if ($outf == "ranked" || $outf == "byterms" || $outf == "bychunks") {
	if ($outf == "ranked") {
		$getoutput = "SELECT * FROM results ORDER BY correlation DESC";
		//$output = $db->query("select * from results order by correlation desc");
	}
	elseif ($outf == "byterms") {
		$getoutput = "SELECT * FROM results ORDER BY term ASC, doc ASC";
		//$output = $db->query("select * from results order by item1 asc, item2 asc");
	}
	elseif ($outf == "bychunks") {
		$getoutput = "SELECT * FROM results ORDER BY doc ASC, term ASC";
		//$output = $db->query("select * from results order by item2 asc, item1 asc");
	}
	
	$output = mysql_query($getoutput);	
	$outputcount = 0;
	//fwrite($log, "output array initialized. ".memory_get_usage()."\n");
	
	echo "<table cellpadding=5>";
	while ($outrow = mysql_fetch_row($output)) {
		//
		$corr = $outrow[0];
		$term_id = $outrow[1];
		$chunk_id = $outrow[2];
		
		$term = $termlist[$term_id];
		$chunktitle = $chunks[$chunk_id][1];
		
		$termisthere = 1;
		if ($scope == "presence"|| $scope == "presentonly") {
			$termisthere = test_for_presence($frags, $term, $chunk_id);
		}

		if ($termisthere == 1) {
			echo "<tr><td>".$chunktitle."</td><td>~</td><td>".$term."</td>";
			echo "<td><a href ='".$displayscript.$chunk_id."\", \"".$term."\", \"".$corr."\")'>";
			echo $corr;
			echo "</a></td>";
			if ($scope == "presence") {
				echo "<td></td>";
			}
		}
		else {
			if ($scope == "presence") {
				echo "<tr><td>".$chunktitle."</td><td>~</td><td>".$term."</td>";
				echo "<td><a href ='".$displayscript.$chunk_id."\", \"".$term."\", \"".$corr."\")'>";
				echo $corr;
				echo "</a></td>";
				echo "<td>(not present)</td>";
			}
		}
		echo "</tr>";
		$outputcount++;
	}
	
	if ($outputcount == 0) {
		echo "<tr><td>No results greater than or equal to ".$bound." were found.</td></tr>";
	}
	echo "</table>";
	
	mysql_free_result($output);
}
else if ($outf == "TDcsv") {
	// Make a csv file for use in Excel to make an XY chart with X as docIds and corrs as Ys
	//$output2 = $db->query("select * from results order by item1 asc");
	$csvoutputselect = "SELECT * FROM results ORDER BY term ASC";
	$output2 = mysql_query($csvoutputselect);
	
	// We have to know how many chunks there are in selected. Each gets one column. Add two columns for
	// the document names and for the chunk IDs.
	$numchunks = count($selected);
	
	// make  as a filler to represent each row in the results table
	$thisRow = array();
	$notHere = array();
	
	for ($iC = 0; $iC < $numchunks; $iC++) {
		$thisRow[$iC] = "";
		if ($scope == "presence") {
			$notHere[$iC] = "";
		}
	}
	$rowCount = count($thisRow);
	
	// prepare the csv file
	$newcsv = "xy-".$hash_value.".csv";
	$downloadpath = "graphs/$newcsv";
	$csv = fopen("graphs/$newcsv", 'w');
	if ($csv == '' || $csv == 0) {
		fwrite($log, "Can't open csv file.\n");
	}
	chmod($csv, 0666);  # make sure the file is user/group writable.
	
	// First row will have the term headers
	fwrite($csv, '"","",""');
	for ($iT = 0; $iT < $numchunks; $iT++) {
		$chunknn = $chunks[$selected[$iT]][0];
		$chunkname = preg_replace("/,/", ".", $chunknn);
		fwrite($csv, ",");
		fwrite($csv, $chunkname);
		if ($scope == "presence") {
			fwrite($csv, ",");
			fwrite($csv, "like ".$chunkname);
		}
	}
	fwrite($csv, "\n");
	
	// Other rows will have the chunk IDs and term correlations
	while ($outrow2 = mysql_fetch_row($output2)) {
		// check the bound
		$corr = $outrow2[0];
		
		// test the bound
		if ($corr < $bound) {
			continue;
		}

		for ($iD = 0; $iD < $numchunks; $iD++) {
			$thisRow[$iD] = "";
			$notHere[$iD] = "";
		}
		
		// collect the information
		$termID = $outrow2[1];
		$chunkID = $outrow2[2];
		$term = $termlist[$termID];
		$chunktitle = $chunks[$chunkID][1];
		$chunkname = preg_replace("/,/", ".", $chunktitle);
		
		$termIsThere = 1;
		// everything is reported unless the user tests for present/not present
		if ($scope == "presence" || $scope = "presentonly") {
			$termIsThere = test_for_presence($frags, $term, $chunkID); 
		}
		
		// put $corr into the array
		$chunkkey = $chunkindex[$chunkID];
		// write to file
		if ($termIsThere == 1) {
			$thisRow[$chunkkey] = $corr;
			// now output the line to $csv
			fwrite($csv, $term.",");
			fwrite($csv, $chunkname.",");
			fwrite($csv, $termID);
			// now print the corrs to file
			for ($iE = 0; $iE < $numchunks; $iE++) {
				fwrite($csv, ",".$thisRow[$iE]);
				if ($scope == "presence") {
					// if user is testing for presence, must fill the row
					fwrite($csv, ",".$notHere[$iE]);
				}
			}
			fwrite($csv, "\n");
		}
		else {
			// term is not there, must exclude if "presentonly"
			if ($scope == "presence") {
				// only print if user wants to see "not present" contexts
				$notHere[$chunkkey] = $corr;
				// now output the line to $csv
				fwrite($csv, $term.",");
				fwrite($csv, $chunkname.",");
				fwrite($csv, $termID);
				// now print the corrs to file
				for ($iE = 0; $iE < $numchunks; $iE++) {
					fwrite($csv, ",".$thisRow[$iE]);
					fwrite($csv, ",".$notHere[$iE]);
				}
				fwrite($csv, "\n");
			}
			// excludes "presentonly"
		}
	}
	fclose($csv);		
	// change the permissions for the new graph file
	chmod("graphs/".$newcsv, 0644);
	
	echo  "<br/><br/>(Right-click the link and use <em>Save link as...</em> to get a copy of a CSV file for viewing an XY Chart in Excel or another program.)<br/><br/>
				<table>
				<tr><td>XY scatterplot CSV file:</td><td><a href='".$downloadpath."'>Link for download.</a> Give it a new name.</td></tr>
				</table><br/><br/>";
	
	mysql_free_result($output2);
}

//  clean up
unset($termlist);
unset($chunks);
//mysql_free_result($output);
mysqli_close($connection);
fwrite($log, "quitting, memory now ".memory_get_usage().".\n");
fclose($log);
return;

?>